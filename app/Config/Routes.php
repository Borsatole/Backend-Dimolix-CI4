<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


// Rotas de Login
$routes->group('login', function ($routes) {
    $routes->post('/', 'AuthController::login');
    $routes->post('validar', 'AuthController::validarToken');

});

// Rotas de Usuarios
$routes->group('usuarios', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'UsuarioController::index',
        ['filter' => 'permission:usuario.visualizar']
    );

    $routes->post(
        '',
        'UsuarioController::create',
        ['filter' => 'permission:usuario.criar']
    );

    $routes->get(
        '(:num)',
        'UsuarioController::show/$1',
        ['filter' => 'permission:usuario.visualizar']
    );

    $routes->post(
        '(:num)',
        'UsuarioController::update/$1',
        ['filter' => 'permission:usuario.editar']
    );

    $routes->delete(
        '(:num)',
        'UsuarioController::delete/$1',
        ['filter' => 'permission:usuario.excluir']
    );

});

// Rotas de Clientes
$routes->group('clientes', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'ClienteController::index',
        ['filter' => 'permission:cliente.visualizar']
    );

    $routes->post(
        '',
        'ClienteController::create',
        ['filter' => 'permission:cliente.criar']
    );

    $routes->get(
        '(:num)',
        'ClienteController::show/$1',
        ['filter' => 'permission:cliente.visualizar']
    );

    $routes->put(
        '(:num)',
        'ClienteController::update/$1',
        ['filter' => 'permission:cliente.editar']
    );

    $routes->delete(
        '(:num)',
        'ClienteController::delete/$1',
        ['filter' => 'permission:cliente.excluir']
    );

});

// Rotas de Enderecos
$routes->group('enderecos', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'EnderecosController::index',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->post(
        '',
        'EnderecosController::create',
        ['filter' => 'permission:enderecos.criar']
    );

    $routes->get(
        '(:num)',
        'EnderecosController::show/$1',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->put(
        '(:num)',
        'EnderecosController::update/$1',
        ['filter' => 'permission:enderecos.editar']
    );

    $routes->delete(
        '(:num)',
        'EnderecosController::delete/$1',
        ['filter' => 'permission:enderecos.excluir']
    );

});

// Rota de estoque
$routes->group('estoque', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'ItensLocacoesController::index',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->post(
        '',
        'ItensLocacoesController::create',
        ['filter' => 'permission:enderecos.criar']
    );

    $routes->get(
        '(:num)',
        'ItensLocacoesController::show/$1',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->put(
        '(:num)',
        'ItensLocacoesController::update/$1',
        ['filter' => 'permission:enderecos.editar']
    );

    $routes->delete(
        '(:num)',
        'ItensLocacoesController::delete/$1',
        ['filter' => 'permission:enderecos.excluir']
    );
});

// Rota de locacoes
$routes->group('locacoes', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'LocacoesController::index',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->post(
        '',
        'LocacoesController::create',
        ['filter' => 'permission:enderecos.criar']
    );

    $routes->get(
        '(:num)',
        'LocacoesController::show/$1',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->put(
        '(:num)',
        'LocacoesController::update/$1',
        ['filter' => 'permission:enderecos.editar']
    );

    $routes->delete(
        '(:num)',
        'LocacoesController::delete/$1',
        ['filter' => 'permission:enderecos.excluir']
    );
});

// Rota de agendamentos
$routes->group('agendamentos', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'AgendamentosController::index',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->post(
        '',
        'AgendamentosController::create',
        ['filter' => 'permission:enderecos.criar']
    );

    $routes->get(
        '(:num)',
        'AgendamentosController::show/$1',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->put(
        '(:num)',
        'AgendamentosController::update/$1',
        ['filter' => 'permission:enderecos.editar']
    );

    $routes->delete(
        '(:num)',
        'AgendamentosController::delete/$1',
        ['filter' => 'permission:enderecos.excluir']
    );
});

$routes->group('financeiro', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'FinanceiroController::index',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->post(
        '',
        'FinanceiroController::create',
        ['filter' => 'permission:enderecos.criar']
    );

    $routes->get(
        '(:num)',
        'FinanceiroController::show/$1',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->put(
        '(:num)',
        'FinanceiroController::update/$1',
        ['filter' => 'permission:enderecos.editar']
    );

    $routes->delete(
        '(:num)',
        'FinanceiroController::delete/$1',
        ['filter' => 'permission:enderecos.excluir']
    );
});
$routes->group('financeiro-categorias', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'CategoriaFinanceiroController::index',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->post(
        '',
        'CategoriaFinanceiroController::create',
        ['filter' => 'permission:enderecos.criar']
    );

    $routes->get(
        '(:num)',
        'CategoriaFinanceiroController::show/$1',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->put(
        '(:num)',
        'CategoriaFinanceiroController::update/$1',
        ['filter' => 'permission:enderecos.editar']
    );

    $routes->delete(
        '(:num)',
        'CategoriaFinanceiroController::delete/$1',
        ['filter' => 'permission:enderecos.excluir']
    );
});

$routes->group('financeiro-contas-fixas', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'FinanceiroContasFixasController::index',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->post(
        '',
        'FinanceiroContasFixasController::create',
        ['filter' => 'permission:enderecos.criar']
    );

    $routes->get(
        '(:num)',
        'FinanceiroContasFixasController::show/$1',
        ['filter' => 'permission:enderecos.visualizar']
    );

    $routes->put(
        '(:num)',
        'FinanceiroContasFixasController::update/$1',
        ['filter' => 'permission:enderecos.editar']
    );

    $routes->delete(
        '(:num)',
        'FinanceiroContasFixasController::delete/$1',
        ['filter' => 'permission:enderecos.excluir']
    );
});

// Rotas de Niveis
$routes->group('papeis', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'NiveisController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'NiveisController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->get(
        '(:num)',
        'NiveisController::show/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->put(
        '(:num)',
        'NiveisController::update/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'NiveisController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );

});

// Rotas de Permissoes
$routes->group('permissoes', ['filter' => 'autenticacao'], function ($routes) {
    $routes->get(
        '',
        'PermissoesController::index',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->get(
        '(:num)',
        'PermissoesController::byNivel/$1',
        ['filter' => 'permission:papeis.visualizar']
    );

    $routes->post(
        '',
        'PermissoesController::create',
        ['filter' => 'permission:papeis.criar']
    );

    $routes->put(
        '(:num)',
        'PermissoesController::updateByNivel/$1',
        ['filter' => 'permission:papeis.editar']
    );

    $routes->delete(
        '(:num)',
        'PermissoesController::delete/$1',
        ['filter' => 'permission:papeis.excluir']
    );

});

