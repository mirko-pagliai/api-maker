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

use ApiMaker\ClassesExplorer;
use ApiMaker\Reflection\Entity\ClassEntity;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tools\Event\EventDispatcherTrait;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * ApiMaker
 */
class ApiMaker
{
    use EventDispatcherTrait;

    /**
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    public $Filesystem;

    /**
     * @var \ApiMaker\ClassesExplorer
     */
    protected $ClassesExplorer;

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
    protected $path;

    /**
     * @var string
     */
    protected $templatePath = ROOT . DS . 'templates' . DS . 'default';

    /**
     * Construct
     * @param string $path Path from which to read the sources
     * @param array $options Options array
     */
    public function __construct(string $path, array $options = [])
    {
        $this->path = $path;

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
        $this->ClassesExplorer = new ClassesExplorer($path);
        $this->Filesystem = new Filesystem();
    }

    /**
     * Sets the default options
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver An `OptionsResolver` instance
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $titleFromPath = array_value_last(array_filter(explode(DS, $this->path)));

        $resolver->setDefaults([
            'debug' => false,
            'title' => $titleFromPath ?? 'My project',
        ]);
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

        //Builds the menu
        $menu = $this->buildMenu($classes, $functions);

        $project = array_intersect_key($this->options, array_flip(['title']));

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
