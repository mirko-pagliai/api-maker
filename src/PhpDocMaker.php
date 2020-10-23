<?php
declare(strict_types=1);

/**
 * This file is part of php-doc-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/php-doc-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace PhpDocMaker;

use League\CommonMark\CommonMarkConverter;
use PhpDocMaker\ClassesExplorer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tools\Event\EventDispatcherTrait;
use Tools\Exceptionist;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

/**
 * PhpDocMaker
 */
class PhpDocMaker
{
    use EventDispatcherTrait;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var string
     */
    protected static $templatePath = ROOT . DS . 'templates' . DS . 'default';

    /**
     * Construct
     * @param string $source Path from which to read the sources
     * @param string $target Target directory where to write the documentation
     * @param array $options Options array
     */
    public function __construct(string $source, string $target, array $options = [])
    {
        $this->source = add_slash_term($source);
        $this->target = add_slash_term($target);

        $this->setOption($options);
    }

    /**
     * "Get" magic method.
     *
     * Allows secure access to the class properties.
     * @param string $name Property name
     * @return mixed Property value
     * @throws \Tools\Exception\PropertyNotExistsException
     */
    public function __get(string $name)
    {
        Exceptionist::objectPropertyExists($this, $name);

        return $this->$name;
    }

    /**
     * Sets the default options
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver An `OptionsResolver` instance
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $titleFromPath = array_value_last(array_filter(explode(DS, $this->source)));

        $resolver->setDefaults([
            'cache' => true,
            'debug' => false,
            'title' => $titleFromPath ?? 'My project',
        ]);
        $resolver->setAllowedTypes('cache', 'bool');
        $resolver->setAllowedTypes('debug', 'bool');
        $resolver->setAllowedTypes('title', ['null', 'string']);
    }

    /**
     * Sets options at runtime.
     *
     * It's also possible to pass an array with names and values to set multiple
     *  options at the same time.
     * @param string|array $name Option name or an array with names and values
     * @param mixed $value Value
     * @return \self
     */
    public function setOption($name, $value = null)
    {
        $options = (is_array($name) ? $name : [$name => $value]) + $this->options;
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        return $this;
    }

    /**
     * Gets the template path
     * @return string
     */
    public static function getTemplatePath(): string
    {
        return add_slash_term(self::$templatePath);
    }

    /**
     * Gets the `Twig` instance
     * @param bool $debug Debug
     * @return \Twig\Environment
     */
    public function getTwig(bool $debug = false): Environment
    {
        $loader = new FilesystemLoader(self::getTemplatePath());
        $twig = new Environment($loader, compact('debug') + [
            'autoescape' => false,
            'strict_variables' => true,
        ]);
        $twig->addFilter(new TwigFilter('is_url', 'is_url'));
        $twig->addFilter(new TwigFilter('to_html', function (string $string) {
            return trim((new CommonMarkConverter())->convertToHtml($string));
        }));

        if ($debug) {
            $twig->addExtension(new DebugExtension());
        }

        return $twig;
    }

    /**
     * Builds
     * @return void
     */
    public function build(): void
    {
        $ClassesExplorer = new ClassesExplorer($this->source);
        $Filesystem = new Filesystem();
        $Twig = $this->getTwig($this->options['debug']);
        $Twig->addGlobal('project', array_intersect_key($this->options, array_flip(['title'])));

        $Filesystem->mkdir($this->target, 0755);

        //Handles temporary directory
        $temp = $this->target . 'temp' . DS;
        $Filesystem->mkdir($temp, 0755);
        $Twig->getLoader()->addPath($temp, 'temp');

        //Handles cache
        $cache = $this->target . 'cache' . DS;
        if ($this->options['cache']) {
            $Filesystem->mkdir($cache, 0755);
            $Twig->setCache($cache);
        } else {
            unlink_recursive($cache, false, true);
        }

        //Gets all classes
        $classes = $ClassesExplorer->getAllClasses();
        $this->dispatchEvent('classes.founded', [$classes]);

        //Gets all functions
        $functions = $ClassesExplorer->getAllFunctions();
        $this->dispatchEvent('functions.founded', [$functions]);

        //Renders partial index page;
        $this->dispatchEvent('index.rendering');
        $output = $Twig->render('elements/index.twig', compact('classes'));
        $Filesystem->dumpFile($temp . 'partial' . DS . 'index.html', $output);
        $this->dispatchEvent('index.rendered');

        //Renders each partial class page
        foreach ($classes as $class) {
            /** @var \PhpDocMaker\Reflection\Entity\ClassEntity $class */
            $this->dispatchEvent('class.rendering', [$class]);
            $output = $Twig->render('elements/class.twig', compact('class'));
            $Filesystem->dumpFile($temp . 'partial' . DS . 'Class-' . $class->getSlug() . '.html', $output);
            $this->dispatchEvent('class.rendered', [$class]);
        }

        //Renders partial functions page
        if (!$functions->isEmpty()) {
            $this->dispatchEvent('functions.rendering');
            $output = $Twig->render('elements/functions.twig', compact('functions'));
            $Filesystem->dumpFile($temp . 'partial' . DS . 'functions.html', $output);
            $this->dispatchEvent('functions.rendered');
        }

        //Gets errors
        $errors = ErrorCatcher::getAll();

        //Renders partial errors page
        if (!$errors->isEmpty()) {
            $this->dispatchEvent('errors.rendering');
            $output = $Twig->render('elements/errors.twig', compact('errors'));
            $Filesystem->dumpFile($temp . 'partial' . DS . 'errors.html', $output);
            $this->dispatchEvent('errors.rendered');
        }

        //Renders menu, topbar and footer for the layout
        $output = $Twig->render('layout/topbar.twig', ['errorsCount' => $errors->count()]);
        $Filesystem->dumpFile($temp . 'layout' . DS . 'topbar.html', $output);
        $output = $Twig->render('layout/footer.twig');
        $Filesystem->dumpFile($temp . 'layout' . DS . 'footer.html', $output);
        $output = $Twig->render('layout/menu.twig', compact('classes') + ['hasFunctions' => !$functions->isEmpty()]);
        $Filesystem->dumpFile($temp . 'layout' . DS . 'menu.html', $output);

        //Renders final index page
        $output = $Twig->render('page.twig', [
            'content' => file_get_contents($temp . 'partial' . DS . 'index.html'),
            'title' => 'Classes index',
        ]);
        $Filesystem->dumpFile($this->target . 'index.html', $output);

        //Renders each final class page
        foreach ($classes as $class) {
            /** @var \PhpDocMaker\Reflection\Entity\ClassEntity $class */
            $output = $Twig->render('page.twig', [
                'content' => file_get_contents($temp . 'partial' . DS . 'Class-' . $class->getSlug() . '.html'),
                'title' => sprintf('%s %s', $class->getType(), $class->getName()),
            ]);
            $Filesystem->dumpFile($this->target . 'Class-' . $class->getSlug() . '.html', $output);
        }

        //Renders final functions page
        if (!$functions->isEmpty()) {
            $output = $Twig->render('page.twig', [
                'content' => file_get_contents($temp . 'partial' . DS . 'functions.html'),
                'title' => 'Functions index',
            ]);
            $Filesystem->dumpFile($this->target . 'functions.html', $output);
        }

        //Renders final errors page
        if (!$errors->isEmpty()) {
            $output = $Twig->render('page.twig', [
                'content' => file_get_contents($temp . 'partial' . DS . 'errors.html'),
                'title' => 'Errors index',
            ]);
            $Filesystem->dumpFile($this->target . 'errors.html', $output);
        }

        unlink_recursive($temp, false, true);

        //Copies assets files
        if (is_readable($this->getTemplatePath() . 'assets')) {
            $Filesystem->mirror($this->getTemplatePath() . 'assets', $this->target . 'assets');
        }
    }
}
