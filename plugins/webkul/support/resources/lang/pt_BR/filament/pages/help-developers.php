<?php

return [
    'tabs' => [
        'help'       => 'Ajuda',
        'developers' => 'Desenvolvedores',
    ],

    'heading' => 'Referência da API',
    'intro'   => 'Conecte seu próprio software aos dados da sua empresa. Cada requisição age como um usuário, com um token, e só vê o que esse usuário pode ver.',
    'general' => 'Geral',
    'empty'   => 'Esta empresa ainda não tem módulos com API. Entrar e sair funciona para todas as empresas.',
    'copy'    => 'Copiar',
    'copied'  => 'Copiado',

    'badges' => [
        'version'   => 'API :name v1',
        'total'     => '{0} Nenhum endpoint|{1} 1 endpoint|[2,*] :count endpoints',
        'in_module' => ':count em :module',
    ],

    'modules' => [
        'label'       => 'Módulos',
        'sales'       => 'Vendas',
        'purchases'   => 'Compras',
        'accounts'    => 'Contabilidade',
        'inventories' => 'Estoque',
        'products'    => 'Produtos',
        'partners'    => 'Contatos',
        'projects'    => 'Projetos',
        'support'     => 'Configurações',
    ],

    'search' => [
        'placeholder' => 'Pesquisar em :module',
        'empty'       => 'Nenhum endpoint corresponde.',
    ],

    'getting_started' => [
        'title'               => 'Primeiros passos',
        'base_url'            => 'URL base',
        'authentication'      => 'Autenticação',
        'authentication_help' => 'Envie seu token no cabeçalho Authorization de cada requisição.',
        'token'               => 'Obter um token',
        'token_page'          => 'Emita um em Configurações › Tokens de API, para um usuário criado para a integração.',
        'token_page_button'   => 'Abrir tokens de API',
        'token_login'         => 'Ou faça a integração entrar com POST /admin/api/v1/login (abaixo).',
        'scopes'              => 'Escopos',
        'scopes_help'         => 'Um token read só pode fazer requisições GET. Criar, alterar ou excluir qualquer coisa exige write.',
        'company'             => 'Qual empresa',
        'company_help'        => 'Envie X-Company-Id com uma das empresas do usuário do token. Sem ele, as requisições vão para a empresa padrão do usuário.',
        'company_current'     => 'A empresa em que você está trabalhando tem o id :id.',
        'limits'              => 'Limites',
        'rate_limit'          => '{1} 1 requisição por minuto por token.|[2,*] :count requisições por minuto por token.',
        'expiry'              => 'Os tokens expiram após :time.',
        'no_expiry'           => 'Os tokens não expiram.',
        'format'              => 'Formato',
        'format_help'         => 'Envie sempre este cabeçalho, para que os erros voltem como JSON e não como uma página web.',
    ],

    'full_reference' => [
        'title'       => 'Referência completa',
        'description' => 'Todos os módulos e endpoints, com um console para testar requisições. Abre em uma nova aba e exige que você esteja conectado.',
        'button'      => 'Abrir referência completa',
    ],

    'headers' => [
        'authorization' => 'Bearer seguido do seu token.',
        'company'       => 'O id da empresa em que trabalhar.',
        'accept'        => 'application/json',
    ],

    'in' => [
        'path'   => 'caminho',
        'query'  => 'consulta',
        'body'   => 'corpo',
        'header' => 'cabeçalho',
    ],

    'endpoint' => [
        'parameters'    => 'Parâmetros',
        'name'          => 'Nome',
        'type'          => 'Tipo',
        'description'   => 'Descrição',
        'required'      => 'Obrigatório',
        'optional'      => 'Opcional',
        'samples'       => 'Exemplos de requisição',
        'response'      => 'Resposta (:status)',
        'other_answers' => 'Outras respostas:',
        'no_token'      => 'Sem token',
    ],

    'answers' => [
        '401' => '401 sem token, ou com um token expirado',
        '403' => '403 o usuário ou a empresa do token não pode fazer isso',
        '422' => '422 os dados enviados não são válidos, e errors nomeia cada campo',
        '429' => '429 requisições demais, aguarde um minuto',
    ],
];
