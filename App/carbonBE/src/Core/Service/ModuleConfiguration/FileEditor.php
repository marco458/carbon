<?php

declare(strict_types=1);

namespace Core\Service\ModuleConfiguration;

use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;

final class FileEditor
{
    public function removeModuleFromOtherModules(string $module, string $path): void
    {
        $finder = new Finder();
        $finder->in($path);
        $finder->exclude(['App']);
        $finder->name('*.php');

        if (str_ends_with($module, 's')) {
            foreach ($finder as $file) {
                $this->removeModuleComponentsFromOtherModules(rtrim($module, 's'), $file->getRealPath());
            }
        }

        foreach ($finder as $file) {
            $this->removeModuleComponentsFromOtherModules($module, $file->getRealPath());
            $this->removeContentFromFile($module, $file->getRealPath());
        }

        if (str_ends_with($module, 's')) {
            foreach ($finder as $file) {
                $this->removeContentFromFile(rtrim($module, 's'), $file->getRealPath());
            }
        }
    }

    public function removeContentFromFile(string $module, string $filePath): void
    {
        if (false === file($filePath)) {
            return;
        }

        $fileLines = file($filePath);

        foreach ($fileLines as $key => $line) {
            if (str_contains($line, $module)) {
                if (array_key_exists($key - 1, $fileLines) && str_starts_with(trim($fileLines[$key - 1]), '#')) {
                    unset($fileLines[$key - 1]);
                    if (array_key_exists($key + 1, $fileLines) && '' === trim($fileLines[$key + 1])) {
                        unset($fileLines[$key + 1]);
                    }
                }
                unset($fileLines[$key]);
            }
        }

        file_put_contents($filePath, implode('', $fileLines));
    }

    public function removeModuleComponentsFromOtherModules(string $module, string $filePath): void
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        /** @var string $code */
        $code = file_get_contents($filePath);
        $ast = $parser->parse($code);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new ModuleNodeVisitor($module, $filePath));
        $traverser->traverse($ast); // @phpstan-ignore-line
    }

    public function removeExampleModuleFromOtherModules(
        array $entities,
        string $path,
    ): void {
        $finder = new Finder();
        $finder->in($path);
        $finder->exclude(['App']);
        $finder->name('*.php');

        foreach ($entities as $entity) {
            foreach ($finder as $file) {
                $this->removeModuleComponentsFromOtherModules($entity, $file->getRealPath());
            }
        }

        foreach ($entities as $entity) {
            foreach ($finder as $file) {
                $this->removeContentFromFile($entity, $file->getRealPath());
            }
        }
    }
}
