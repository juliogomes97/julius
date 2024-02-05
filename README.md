## Index
- Básico
  - [Primeiro passos](#primeiro-passos)
  - [Rota mais simples](#rota-mais-simples)
  - [Métodos](#métodos)
  - [Parâmetros](#parâmetros)
  - [Multi Parâmetros](#multi-parâmetros)
  - [Grupos](#grupos)
  - [Grupos com Parâmetros](#grupos-com-parâmetros)
  - [Rota não encontrada](#rota-não-encontrada)
- Avançado
  - [MasterController](#mastercontroller)
  - [Criar controlador](#criar-controlador)
  - [Parâmetros no controlador](#parâmetros-no-controlador)

# Básico
## Primeiro passos
Antes de começar usar o sistema de rotas deve iniciar a método boot()
``` php
use Julius\Framework\Http\Request;
use Julius\Framework\Routing\Router;

Router::boot(new Request);

// Rotas ...
```
> [!TIP]
> As tuas rotas deve estar no index.php

## Rota mais simples
Aqui temos um exemplo de uma rota mais simples possível, sem complicações
``` php
// Router::boot(...)

Router::get('/bem-vindo', function(Request $request) {
  echo 'Olá mundo!';
});
```
> [!IMPORTANT]
> Todas as rotas devem começar com `/`
## Métodos
A class `Router` disponibiliza alguns métodos para controle de rota:
``` php
Router::get();
Router::post();
Router::put();
Router::patch();
Router::delete();
Router::options();
```
Tambem pode usar o método `add()` que o seu primeiro parâmetros será o tipo de método
``` php
Router::add('GET', ...);
Router::add('POST', ...);

// Outros ....
```
## Parâmetros
Gostamos de passar parâmetros no `uri` como o nome do utilizador ou id de uma postagem, para fazer-mos isso é bastante simples
``` php
Router::get('/utilizador/:name', function(Request $request, string $name) {
  echo "Olá {$name}";
});
```
> [!WARNING]
> Atenção! O primeiro parâmetro será sempre do tipo `Julius\Framework\Http\Request`, em seguida é que vem os parâmetros customizados.

Podemos definir que tipos de dados queremos que o parâmetro aceite, no exemplo acima ele por defeito aceita qualquer tipo de palavra ou numeros, mas agora vou criar um parâmetro onde só quero que aceite numeros
``` php
Router::get('/postagem/:id', function(Request $request, string $postagem_id) {
  echo "Esta postagem tem o ID: {$postagem_id}";
}, ['id' => ([0-9]+));
```
## Multi Parâmetros
Podemos usar numero de parâmetros que for necessario
``` php
Router::get('/utilizador/:name/postagem/:id', function(Request $request, string $name, string $id) {
  echo "O {$name} tem uma postagem com o ID: {$id}";
}, ['name' => '([a-zA-Z]+)', 'id' => ([0-9]+));
```
## Grupos
Para um código mais limpo e facil de ler era bom agrupar as rotas, não é? Então vamos fazer isso
``` php
Router::group('/painel', function() {
  // Rota: /painel
  Router::get('/', function(Request $request) {
    echo "Aqui é o landing page ao acessar /painel";
  });
  // Rota: /painel/funcionarios
  Router::get('/funcionarios', function(Request $request) {
    echo "Estamos na página dos funcionarios";
  });
  // Rota: /painel/configuracoes
  Router::get('/configuracoes', function(Request $request) {
    echo "Estamos na página das configurações";
  });
});
```
Aqui temos 3 rotas dentro do grupo `painel`, para acessar basta aceder `/painel`, `/painel/functionarios` ou `/painel/configuracoes`

> [!NOTE]
> Se dentro do grupo não tiver nenhuma landing page ele vai dar como rota não encontrada, no exemplo acima temos uma rota que uri é `/`, portante ao aceder `/painel` ele chama essa rota.

> [!TIP]
> No `uri` da função `group()` não é obrigatorio usar o `/` logo ao inicio, como nas rotas são obrigadas usar o `/` logo de inicio

# Grupos com Parâmetros
O grupo tambem pode receber parâmetros, vamos ver um exemplo
``` php
Router::group('utilizador/:id', function() {
  // Rota: /utilizador/123 <- qualquer coisa
  Router::get('/', function(Request $request) {
    echo "Aqui é o landing page do utilizador/id";
  });
  // Rota: /utilizador/456/postagens
  Router::get('/postagens', function(Request $request) {
    echo "Estamos na página de postagens do utilizador 456";
  });
  // Rota: /utilizador/780/fotos
  Router::get('/fotos', function(Request $request) {
    echo "Estamos na página de fotos do utilizador 780";
  });
});
```
Só consigo aceder ao grupo a cima se eu entrar em `utilizador/id-do-utilizador` depois disso posso aceder `/utilizador/123`, `/utilizador/456/postagens` ou `/utilizador/780/fotos`
> [!TIP]
> O método `group()` não consegue controlar o que entra no parâmetro `:id`, só nas rotas que podem controlar [Ver Parâmetros](#parâmetros)

Exemplo controlar `:id` do exemplo em cima
``` php
Router::group('utilizador/:id', function() {
  $regex = ['id' => '([0-9]+)'];

  // Rota: /utilizador/123 <- qualquer coisa
  Router::get('/', function(Request $request) {
    echo "Aqui é o landing page do utilizador/id";
  }, $regex);
  // Rota: /utilizador/456/postagens
  Router::get('/postagens', function(Request $request) {
    echo "Estamos na página de postagens do utilizador 456";
  }, $regex);
  // Rota: /utilizador/780/fotos
  Router::get('/fotos', [\MyApp\Controllers\UserPhotosController::class, 'index'], $regex);
});
```
> [!NOTE]
> Aqui está a ser controlado pela variavel `$regex` que neste caso só aceita numeros

## Rota não encontrada
E se as tuas rotas que definiste não forem encontras ou o utilizador digitar mal a `url`? Ai é que entra o método `fallback()`, o `fallback` é um método que só é chamado caso nenhuma rota for encontrada.
``` php
// Router::boot() ....
// Rotas ...

Router::fallback(function(Request $request){
  echo "Nenhuma das rotas foram encontras!";
});
```
> [!WARNING]
> Atenção! Este função deve estar no fim de todas as rotas.


# Avançado
Já reparaste que a gente usa sempre um `callable` quando adicionamos uma rota? Imagina que cada rota tem um script muito grande, o código não ficava bom para ler, certo?
``` php
Router::get('/postagens', function(Request $request) {
  // código ...
});
```
Então em vez de usar `callable`, vamos usar o um **Controlador**.

## MasterController
O `Julius Framework` já tem um controlador para ser utlizado, mas ele não pode ser utlizado diretamente no Router, nós devemos criar os proprios controladores e fazer abstração do **MasterController**, no `__construct` dele já contém o parâmetro `Request`.
> [!WARNING]
> O `__construct()` do `MasterController` não vai receber os parâmetros da `uri`, simplesmente recebe só `Request`, quem vai receber os parâmetros, são as funções dos controlador que seram chamados no `Router`
## Criar Controlador
Para termos um código mais limpo e legivel, vamos criar uma pasta chamada `Controllers`, em seguida vamos criar 2 controlados um que se vai se chamar `LandingController.php` e outro `NotFoundController.php`.

``` php
// LandingController.php

namespace MyApp\Controllers;

use Julius\Framework\Controllers\MasterController;

class LandingController extends MasterController
{
    public function index() : void
    {
        // Podes usar o Request, basta aceder '$this->request'
        echo "Olá mundo!";
    }
}

```
``` php
// NotFoundController.php

namespace MyApp\Controllers;

use Julius\Framework\Controllers\MasterController;

class NotFoundController extends MasterController
{
    public function index() : void
    {
        echo "Página não encontrada";
    }
}
```
Agora vamos fazer a chamada no `Router`.
``` php
// Router::boot(...)

Router::get('/bem-vindo', [\MyApp\Controllers\LandingController::class, 'index']);
// Outras rotas ....

Router::fallback([\MyApp\Controllers\NotFoundController::class, 'index']);
```

> [!IMPORTANT]
> O `Router` pode receber tanto um `array` onde contém o *nome da class* (`LandingController` ou `NotFoundController`) e o *nome do método* (`index`) ou um `callable` como temos usado ao inicio.

## Parâmetros no controlador
Agora como posso usar os parâmetros utilizando controladores?

``` php
// UserController.php

namespace MyApp\Controllers;

use Julius\Framework\Controllers\MasterController;

class UserController extends MasterController
{
    public function getUser(string $utilizador_id) : void
    {
        echo "Utilizador com ID: {$utilizador_id}";
    }
}

```
``` php
// index.php

Router::get('/utilizador/:id', [\MyApp\Controller\UserController::class, 'getUser']);
```

No método que criei com o nome `getUser` vai receber um parâmetro `:id`

> [!IMPORTANT]
> Brevemente vai haver mais atualizações na documentação sobre este framework ;)