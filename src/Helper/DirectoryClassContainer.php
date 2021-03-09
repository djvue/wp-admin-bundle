<?php


namespace Djvue\WpAdminBundle\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\Finder;

class DirectoryClassContainer implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getClasses(string $baseNamespace): array
    {
        $finder = new Finder();
        $path = str_replace(['App\\', '\\'], ['src' . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $baseNamespace);
        $finder = $finder->in($this->parameterBag->get('kernel.project_dir') . DIRECTORY_SEPARATOR . $path);
        $collection = new ArrayCollection(array_values(iterator_to_array($finder)));
        return $collection
            ->filter(fn(\SplFileInfo $node) => $node->isFile())
            ->map(function ($node) use ($baseNamespace) {
                $className = str_replace('.php', '', $node->getBasename());
                $className = $baseNamespace . '\\' . $className;
                if ($this->isAbstract($className)) {
                    return null;
                }
                return $this->container->get($className);
            })
            ->filter(fn($el) => $el !== null)
            ->getValues()
        ;
    }

    private function isAbstract(string $className): bool
    {
        try {
            return (new \ReflectionClass($className))->isAbstract();
        } catch (\ReflectionException $exception) {
            return false;
        }
    }
}
