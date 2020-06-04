<?php
declare(strict_types=1);

/**
 * This file is part of api-maker.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/api-maker
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */
namespace ApiMaker;

use ApiMaker\Reflection\Entity\ClassEntity;
use ApiMaker\ReflectorExplorer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * ApiMaker
 */
class ApiMaker
{
    /**
     * @var \ApiMaker\ReflectorExplorer
     */
    protected $ReflectorExplorer;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string
     */
    protected $templatePath = ROOT . DS . 'templates' . DS . 'default';

    /**
     * Construct
     * @param string|array $paths Path or paths from which to read the sources
     * @param array $options Options array
     */
    public function __construct($paths, array $options = [])
    {
        $this->ReflectorExplorer = new ReflectorExplorer((array)$paths);

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Sets the default options
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver An `OptionsResolver` instance
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['title' => 'My project']);
    }

    /**
     * Internal method to build the menu.
     *
     * It gets all classes and all functions, builds and returns a menu.
     * @param array $classes Classes, array of `ClassEntity`
     * @param array $functions Functions, array of `FunctionEntity`
     * @return array Menu, array with names and files
     */
    protected function buildMenu(array $classes, array $functions): array
    {
        $menu = array_map(function (ClassEntity $class) {
            return ['name' => $class->getName(), 'link' => $class->getLink()];
        }, $classes);

        if ($functions) {
            $menu[] = ['name' => 'functions', 'link' => 'functions.html'];
        }

        return $menu;
    }

    /**
     * Gets a Twig `Environment` instance
     * @return \Twig\Environment
     */
    protected function getTwigInstance(): Environment
    {
        $loader = new FilesystemLoader($this->templatePath);
        $twig = new Environment($loader, [
            'autoescape' => false,
            'debug' => true,
            'strict_variables' => true,
        ]);
        $twig->addExtension(new DebugExtension());

        return $twig;
    }

    /**
     * Builds
     * @param string $target Target directory where to write the documentation
     * @return void
     */
    public function build(string $target): void
    {
        @mkdir($target, 0755, true);

        //Copies assets files
        if (is_readable($this->templatePath . DS . 'assets')) {
            $filesystem = new Filesystem();
            $filesystem->mirror($this->templatePath . DS . 'assets', $target . DS . 'assets');
        }

        //Gets all classes and all functions
        $classes = $this->ReflectorExplorer->getAllClasses();
        $functions = $this->ReflectorExplorer->getAllFunctions();

        //Builds the menu
        $menu = $this->buildMenu($classes, $functions);

        $project = $this->options;

        //Renders index page
        $template = $this->getTwigInstance()->load('index.twig');
        $output = $template->render(compact('classes', 'menu', 'project'));
        file_put_contents($target . DS . 'index.html', $output);

        //Renders functions page
        if ($functions) {
            $template = $this->getTwigInstance()->load('functions.twig');
            $output = $template->render(compact('functions', 'menu', 'project'));
            file_put_contents($target . DS . 'functions.html', $output);
        }

        //Renders each class page
        $template = $this->getTwigInstance()->load('class.twig');
        foreach ($classes as $class) {
            $outputFile = $target . DS . 'Class-' . $class->getSlug() . '.html';
            $output = $template->render(compact('class', 'menu', 'project'));
            file_put_contents($outputFile, $output);
        }
    }
}
