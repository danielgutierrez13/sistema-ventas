<?php

/*
 * This file is part of the PIDIA.
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace Pidia\Apps\Demo\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pidia\Apps\Demo\Entity\Usuario;
use Pidia\Apps\Demo\Entity\UsuarioPermiso;
use Pidia\Apps\Demo\Security\Security;

/**
 * @method UsuarioPermiso|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioPermiso|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioPermiso[]    findAll()
 * @method UsuarioPermiso[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioPermisoRepository extends ServiceEntityRepository
{
    private $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, UsuarioPermiso::class);
        $this->security = $security;
    }

    public function findMenus(int $usuario_id): array
    {
        $queryBuilder = $this->createQueryBuilder('permiso')
            ->select('menu.nombre as nombre')
            ->addSelect('padre.id as padreId')
            ->addSelect('padre.nombre as padre_nombre')
            ->addSelect('menu.ruta as ruta')
            ->addSelect('permiso.listar as listar')
            ->addSelect('permiso.mostrar as mostrar')
            ->addSelect('permiso.crear as crear')
            ->addSelect('permiso.editar as editar')
            ->addSelect('permiso.eliminar as eliminar')
            ->addSelect('permiso.exportar as exportar')
            ->addSelect('permiso.maestro as maestro')
            ->addSelect('permiso.imprimir as imprimir')
            ->join('permiso.menu', 'menu')
            ->join('menu.config', 'config')
            ->leftJoin('menu.padre', 'padre')
            ->leftJoin('permiso.roles', 'roles')
            ->leftJoin('roles.usuarios', 'usuarios')
            ->where('menu.activo = TRUE')
            ->andWhere('usuarios.id = :usuario_id')
            ->setParameter('usuario_id', $usuario_id)// ->orderBy('padre.peso', 'ASC')
        ;

        $this->security->configQuery($queryBuilder);

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * @deprecated used findPermisosByUsuarioIdAndRuta
     */
    public function findPermisos(Usuario $usuario, ?string $ruta = null): array
    {
        $queryBuilder = $this->createQueryBuilder('permiso')
            ->select('menu.ruta as ruta')
            ->addSelect('permiso.listar as listar')
            ->addSelect('permiso.mostrar as mostrar')
            ->addSelect('permiso.crear as crear')
            ->addSelect('permiso.editar as editar')
            ->addSelect('permiso.eliminar as eliminar')
            ->addSelect('permiso.imprimir as imprimir')
            ->addSelect('permiso.exportar as exportar')
            ->addSelect('permiso.importar as importar')
            ->addSelect('permiso.maestro as maestro')
            ->join('permiso.menu', 'menu')
            ->join('menu.config', 'config')
            ->leftJoin('menu.padre', 'padre')
            ->leftJoin('permiso.roles', 'roles')
            ->leftJoin('roles.usuarios', 'usuarios')
            ->where('menu.activo = TRUE')
            ->andWhere('usuarios.id = :usuario_id')
            ->setParameter('usuario_id', $usuario->getId());

        if (null !== $ruta) {
            $queryBuilder = $queryBuilder
                ->andWhere('menu.ruta = :ruta')
                ->setParameter('ruta', $ruta);
        }

        $this->security->configQuery($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findPermisosByUsuarioIdAndRuta(int $usuarioId, ?string $ruta = null): array
    {
        $queryBuilder = $this->createQueryBuilder('permiso')
            ->select('menu.ruta as route')
            ->addSelect('permiso.listar as list')
            ->addSelect('permiso.mostrar as view')
            ->addSelect('permiso.crear as new')
            ->addSelect('permiso.editar as edit')
            ->addSelect('permiso.eliminar as delete')
            ->addSelect('permiso.imprimir as print')
            ->addSelect('permiso.exportar as export')
            ->addSelect('permiso.importar as import')
            ->addSelect('permiso.maestro as master')
            ->join('permiso.menu', 'menu')
            ->join('menu.config', 'config')
            ->leftJoin('menu.padre', 'padre')
            ->leftJoin('permiso.roles', 'roles')
            ->leftJoin('roles.usuarios', 'usuarios')
            ->where('menu.activo = TRUE')
            ->andWhere('usuarios.id = :usuario_id')
            ->setParameter('usuario_id', $usuarioId);

        if (null !== $ruta) {
            $queryBuilder
                ->andWhere('menu.route = :route')
                ->setParameter('route', $ruta);
        }

        $this->security->configQuery($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @return UsuarioPermiso[]|array
     */
    public function findByMenu(int $menu_id): array
    {
        $queryBuilder = $this->createQueryBuilder('usuario_permiso')
            ->select('usuario_permiso')
            ->join('usuario_permiso.menu', 'menu')
            ->join('menu.config', 'confg')
            ->where('menu.id = :menu_id')
            ->setParameter('menu_id', $menu_id);

        $this->security->configQuery($queryBuilder);

        return $queryBuilder->getQuery()->getResult();
    }
}
