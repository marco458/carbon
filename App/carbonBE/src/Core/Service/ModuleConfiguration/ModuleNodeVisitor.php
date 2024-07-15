<?php

declare(strict_types=1);

namespace Core\Service\ModuleConfiguration;

use PhpParser\Node;
use PhpParser\NodeVisitor;

class ModuleNodeVisitor implements NodeVisitor
{
    private string $module;
    private string $filePath;
    private array $lines;

    public function __construct(
        string $module,
        string $filePath,
        array $lines = [],
    ) {
        $this->module = $module;
        $this->filePath = $filePath;
        $this->lines = $lines;
    }

    public function beforeTraverse(array $nodes): null
    {
        return null;
    }

    public function enterNode(Node $node): null
    {
        if ($node instanceof Node\Stmt\ClassMethod && str_contains($node->name->name, $this->module)) {
            array_unshift($this->lines, [
                'startLine' => $node->getStartLine(),
                'endLine' => $node->getEndLine() + 1,
            ]);
        }

        return null;
    }

    public function afterTraverse(array $nodes): null
    {
        return null;
    }

    public function leaveNode(Node $node): null
    {
        if ($node instanceof Node\Stmt\Class_ && count($this->lines)) { // @phpstan-ignore-line
            $this->deleteMethod($this->lines, $this->filePath);
            $this->lines = [];
        }

        return null;
    }

    public function deleteMethod(array $lines, string $filePath): void
    {
        foreach ($lines as $methodLines) {
            $inputFile = new \SplFileObject($filePath, 'r');
            $outputFile = new \SplFileObject('updatedClass.php', 'w');
            $lineNumber = 1;
            while (!$inputFile->eof()) {
                $line = $inputFile->fgets();

                if ($lineNumber < $methodLines['startLine'] || $lineNumber > $methodLines['endLine'] ||
                    ($lineNumber === $methodLines['endLine'] && (bool) trim($line))) { // @phpstan-ignore-line
                    $outputFile->fwrite($line); // @phpstan-ignore-line
                }

                ++$lineNumber;
            }
            $inputFile = null;
            $outputFile = null;

            rename('updatedClass.php', $filePath);
        }
    }
}
