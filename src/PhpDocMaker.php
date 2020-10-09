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
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    public $Filesystem;

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
    protected static $templatePath = ROOT . DS . 'templates' . DS . 'default';

    /**
     * Construct
     * @param string $source Path from which to read the sources
     * @param array $options Options array
     */
    public function __construct(string $source, array $options = [])
    {
        $this->source = $source;

        $this->setOption($options);
        $this->Filesystem = new Filesystem();
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
     * Gets options
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
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
        $options = (is_array($name) ? $name : [$name => $value]) + $this->getOptions();
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        return $this;
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
     * Gets the template path
     * @return string
     */
    public static function getTemplatePath(): string
    {
        return self::$templatePath;
    }

    /**
     * Builds
     * @param string $target Target directory where to write the documentation
     * @return void
     */
    public function build(string $target): void
    {
        $this->Filesystem->mkdir($target, 0755);
        $ClassesExplorer = new ClassesExplorer($this->source);
        $Twig = $this->getTwig($this->options['debug']);
        $Twig->addGlobal('project', array_intersect_key($this->options, array_flip(['title'])));

        //Handles temporary directory
        $temp = $target . DS . 'temp' . DS;
        $this->Filesystem->mkdir($temp, 0755);
        $Twig->getLoader()->addPath($temp, 'temp');

        //Handles cache
        $cache = $target . DS . 'cache' . DS;
        if ($this->options['cache']) {
            $this->Filesystem->mkdir($cache, 0755);
            $Twig->setCache($cache);
        } else {
            unlink_recursive($cache, false, true);
        }

        //Copies assets files
        if (is_readable($this->getTemplatePath() . DS . 'assets')) {
            $this->Filesystem->mirror($this->getTemplatePath() . DS . 'assets', $target . DS . 'assets');
        }

        //Gets all classes
        $classes = $ClassesExplorer->getAllClasses();
        $this->dispatchEvent('classes.founded', [$classes]);

        //Gets all functions
        $functions = $ClassesExplorer->getAllFunctions();
        $this->dispatchEvent('functions.founded', [$functions]);

        //Renders menu and footer
        $output = $Twig->render('layout/footer.twig');
        $this->Filesystem->dumpFile($temp . 'footer.html', $output);
        $output = $Twig->render('layout/menu.twig', compact('classes') + ['hasFunctions' => !empty($functions)]);
        $this->Filesystem->dumpFile($temp . 'menu.html', $output);

        //Renders index page
        $output = $Twig->render('index.twig', compact('classes'));
        $this->Filesystem->dumpFile($target . DS . 'index.html', $output);
        $this->dispatchEvent('index.rendered');

        //Renders functions page
        if ($functions) {
            $this->dispatchEvent('functions.rendering');
            $output = $Twig->render('functions.twig', compact('functions'));
            $this->Filesystem->dumpFile($target . DS . 'functions.html', $output);
            $this->dispatchEvent('functions.rendered');
        }

        //Renders each class page
        foreach ($classes as $class) {
            $this->dispatchEvent('class.rendering', [$class]);
            $output = $Twig->render('class.twig', compact('class'));
            $this->Filesystem->dumpFile($target . DS . 'Class-' . $class->getSlug() . '.html', $output);
            $this->dispatchEvent('class.rendered', [$class]);
        }

        unlink_recursive($temp, false, true);
    }
}
