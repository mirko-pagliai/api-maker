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

use PhpDocMaker\ClassesExplorer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tools\Event\EventDispatcherTrait;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * PhpDocMaker
 */
class PhpDocMaker
{
    use EventDispatcherTrait;

    /**
     * @var \PhpDocMaker\ClassesExplorer
     */
    protected $ClassesExplorer;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    public $Filesystem;

    /**
     * @var \Twig\Environment
     */
    public $Twig;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $templatePath = ROOT . DS . 'templates' . DS . 'default';

    /**
     * Construct
     * @param string $source Path from which to read the sources
     * @param array $options Options array
     */
    public function __construct(string $source, array $options = [])
    {
        $this->source = $source;

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        $loader = new FilesystemLoader($this->getTemplatePath());
        $this->Twig = new Environment($loader, [
            'autoescape' => false,
            'debug' => $this->options['debug'],
            'strict_variables' => true,
        ]);
        if ($this->options['debug']) {
            $this->Twig->addExtension(new DebugExtension());
        }
        $this->ClassesExplorer = new ClassesExplorer($source);
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
            'debug' => false,
            'title' => $titleFromPath ?? 'My project',
        ]);
    }

    /**
     * Gets the template path
     * @return string
     */
    public function getTemplatePath(): string
    {
        return $this->templatePath;
    }

    /**
     * Builds
     * @param string $target Target directory where to write the documentation
     * @return void
     */
    public function build(string $target): void
    {
        $this->Filesystem->mkdir($target, 0755);
        $this->Twig->getLoader()->addPath($target, 'target');
        $this->Twig->setCache($target . DS . 'cache');

        //Copies assets files
        if (is_readable($this->getTemplatePath() . DS . 'assets')) {
            $this->Filesystem->mirror($this->getTemplatePath() . DS . 'assets', $target . DS . 'assets');
        }

        //Gets all classes
        $classes = $this->ClassesExplorer->getAllClasses();
        $this->dispatchEvent('classes.founded', [$classes]);

        //Gets all functions
        $functions = $this->ClassesExplorer->getAllFunctions();
        $this->dispatchEvent('functions.founded', [$functions]);

        $project = array_intersect_key($this->options, array_flip(['title']));

        //Renders the menu
        $output = $this->Twig->render('layout/menu.twig', compact('classes'));
        $this->Filesystem->dumpFile($target . DS . 'layout' . DS . 'menu.html', $output);
        $this->dispatchEvent('menu.rendered');

        //Renders index page
        $output = $this->Twig->render('index.twig', compact('classes', 'menu', 'project'));
        $this->Filesystem->dumpFile($target . DS . 'index.html', $output);
        $this->dispatchEvent('index.rendered');

        //Renders functions page
        if ($functions) {
            $output = $this->Twig->render('functions.twig', compact('functions', 'menu', 'project'));
            $this->Filesystem->dumpFile($target . DS . 'functions.html', $output);
            $this->dispatchEvent('functions.rendered');
        }

        //Renders each class page
        foreach ($classes as $class) {
            $output = $this->Twig->render('class.twig', compact('class', 'menu', 'project'));
            $this->Filesystem->dumpFile($target . DS . 'Class-' . $class->getSlug() . '.html', $output);
            $this->dispatchEvent('class.rendered', [$class]);
        }
    }
}
