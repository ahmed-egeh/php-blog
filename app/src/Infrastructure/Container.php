<?php
declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Port\AvatarStorage;
use App\Application\Port\CurrentUser;
use App\Application\Port\Mailer;
use App\Application\Port\RememberMe;
use App\Domain\Category\CategoryRepository;
use App\Domain\Post\PostRepository;
use App\Domain\User\UserRepository;
use App\Infrastructure\Auth\CookieRememberMe;
use App\Infrastructure\Auth\SessionCurrentUser;
use App\Infrastructure\Mail\SmtpMailer;
use App\Infrastructure\Persistence\Database;
use App\Infrastructure\Persistence\PdoCategoryRepository;
use App\Infrastructure\Persistence\PdoPostRepository;
use App\Infrastructure\Persistence\PdoUserRepository;
use App\Infrastructure\Storage\LocalAvatarStorage;
use PDO;
use ReflectionClass;
use ReflectionNamedType;
use RuntimeException;

final class Container
{
    /** @var array<class-string, callable(self): object> */
    private array $factories = [];

    /** @var array<class-string, object> */
    private array $instances = [];

    public static function boot(): self
    {
        $container = new self();

        $container->bind(PDO::class, static fn(): PDO => Database::connect());
        $container->bind(UserRepository::class, static fn(self $c): UserRepository => new PdoUserRepository($c->get(PDO::class)));
        $container->bind(PostRepository::class, static fn(self $c): PostRepository => new PdoPostRepository($c->get(PDO::class)));
        $container->bind(CategoryRepository::class, static fn(self $c): CategoryRepository => new PdoCategoryRepository($c->get(PDO::class)));
        $container->bind(Mailer::class, static fn(): Mailer => new SmtpMailer());
        $container->bind(RememberMe::class, static fn(self $c): RememberMe => new CookieRememberMe($c->get(UserRepository::class)));
        $container->bind(AvatarStorage::class, static fn(): AvatarStorage => new LocalAvatarStorage());
        $container->bind(CurrentUser::class, static fn(self $c): CurrentUser => new SessionCurrentUser($c->get(UserRepository::class)));

        return $container;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @param callable(self): T $factory
     */
    public function bind(string $id, callable $factory): void
    {
        $this->factories[$id] = $factory;
    }

    /**
     * @template T of object
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id): object
    {
        if (isset($this->instances[$id])) {
            /** @var T $instance */
            $instance = $this->instances[$id];

            return $instance;
        }

        if (isset($this->factories[$id])) {
            $this->instances[$id] = ($this->factories[$id])($this);

            /** @var T $instance */
            $instance = $this->instances[$id];

            return $instance;
        }

        $this->instances[$id] = $this->autowire($id);

        /** @var T $instance */
        $instance = $this->instances[$id];

        return $instance;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    private function autowire(string $class): object
    {
        $reflection = new ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return $reflection->newInstance();
        }

        $arguments = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw new RuntimeException(
                    "Cannot resolve {$class}::\${$parameter->getName()}."
                );
            }

            $arguments[] = $this->get($type->getName());
        }

        return $reflection->newInstanceArgs($arguments);
    }
}
