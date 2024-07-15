<?php

declare(strict_types=1);

namespace Core\Service;

use Carbon\Carbon;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Kernel;

final readonly class HealthCheckService
{
    private ?Request $request;
    private string $gitDirectory;

    public function __construct(
        private string $appEnv,
        private EntityManagerInterface $entityManager,
        private ?RequestStack $requestStack,
        private string $projectDirectory
    ) {
        /* @phpstan-ignore-next-line */
        $this->request = $this->requestStack->getCurrentRequest();
        $this->gitDirectory = $this->projectDirectory.'/.git';
    }

    /**
     * @throws Exception
     */
    public function collectData(): array
    {
        $doctrine = 'failed';

        $this->entityManager->getConnection()->connect();
        $doctrine = 'running';

        $this->entityManager->getConnection()->close();

        $cpuLoad = false !== sys_getloadavg() ? sys_getloadavg() : 'Could not fetch';

        $composer = (string) exec('composer --version');

        $buildInfo = $this->getBuildInfo();

        return [
            'env' => strtoupper($this->appEnv),
            'php' => PHP_VERSION,
            'symfony' => Kernel::VERSION,
            'doctrine' => $doctrine,
            'composer' => str_replace('Composer version ', '', $composer),
            'cpu' => $cpuLoad,
            'git_branch' => $buildInfo['current_branch'] ?? null,
            'git_commit' => $buildInfo['last_commit_hash'] ?? null,
            'build_date' => $buildInfo['build_date'] ?? null,
            'container_id' => $buildInfo['container_id'] ?? null,
            'container_start_time' => $buildInfo['container_start_time'] ?? null,
        ];
    }

    public function getBuildInfo(): array
    {
        $branchName = 'Unknown';
        $commitHash = 'Unknown';
        $buildDate = 'Unknown';
        $containerId = 'Unknown';
        $containerStartTime = 'Unknown';

        try {
            // 1st attempt - read from $_SERVER
            if ($this->request instanceof Request && null !== $this->request->server->get('VERSION_INFO')) {
                $commitHash = $this->request->server->get('VERSION_INFO');
                $buildDate = $this->request->server->get('BUILD_DATE');
                $containerId = $this->request->server->get('CONTAINER_ID');
                $containerStartTime = $this->request->server->get('CONTAINER_START_TIME');
            } elseif (file_exists($this->gitDirectory.'/HEAD')) {
                $gitHead = file_get_contents($this->gitDirectory.'/HEAD');
                $branchName = rtrim((string) preg_replace("/(.*?\/){2}/", '', (string) $gitHead));
                $branchPath = $this->gitDirectory.'/refs/heads/'.$branchName;
                $commitHash = file_get_contents($branchPath);
                $buildDate = Carbon::createFromTimestamp((int) filemtime($branchPath))->format('Y-m-d h:i:s');
            }
        } catch (\Exception $exception) {
            return [
                'current_branch' => $branchName,
                'last_commit_hash' => $commitHash,
                'build_date' => $buildDate,
                'container_id' => $containerId,
                'container_start_time' => $containerStartTime,
                'error_message' => $exception->getMessage(),
            ];
        }

        return [
            'current_branch' => $branchName,
            'last_commit_hash' => $commitHash,
            'build_date' => $buildDate,
            'container_id' => $containerId,
            'container_start_time' => $containerStartTime,
        ];
    }
}
