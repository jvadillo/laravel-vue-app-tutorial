# Larael Vuejs app
## About Laravel

## Pre-requisitos



## Pasos

### Crear la API con los datos de Productos y Categorias

```
php artisan make:model Category -mcr
```

```php
<?php

public function up()
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}
```

```
php artisan make:model Product -mcr
```

```php
public function up()
{
    Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->constrained();
        $table->string('name');
        $table->text('description');
        $table->integer('price');
        $table->timestamps();
    });
}
```

```
sail artisan migrate
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
```

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'price'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```


```
sail artisan make:factory CategoryFactory
```
```php
public function definition()
{
    return [
        'name' => $this->faker->words(2, true),
    ];
}
```

```
sail artisan make:factory ProductFactory
```

```php
public function definition()
{
    return [
        'name' => $this->faker->word(),
        'category_id' => \App\Models\Category::inRandomOrder()->first()->id,
        'description' => $this->faker->text($maxNbChars = 250),
        'price' => $this->faker->numberBetween(100, 4000),
    ];
}
```

```php
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Category::factory()->create(['name' => 'Informática']);
        Category::factory()->create(['name' => 'Electrónica']);
        Category::factory()->create(['name' => 'Videojuegos']);
        Category::factory()->create(['name' => 'Libros']);
        Category::factory()->create(['name' => 'Otros']);

        Product::factory()->count(40)->create();
    }
}
```

```
sail artisan migrate:refresh --seed
```

Crear los controladores CategoryController y ProductController. A la hora de ejecutar el comando de artisan, añadiremos el sufijo Api/ para que los cree dentro del directorio: 

sail artisan make:controller Api/CategoryController
sail artisan make:controller Api/ProductController

Por el momento los controladores solamente implementarán el método `index` y devolverán todas las categorías y productos respectivamente:

`CategoryController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index (Request $request)
    {
        return Category::all();
    }
}
```

`ProductController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index (Request $request)
    {
        return Product::all();
    }
}
```

En el futuro mejoraremos el código anterior para que los controladores, en lugar de deolver los modelos completos, devuelvan únicamente los atributos que el frontend requiere. 

A continuación crearemos las rutas de `routes/api.php` para comprobar que los datos se muestran correctamente:

```php
Route::get('categories', [CategoryController::class, 'index']);
Route::get('products', [ProductController::class, 'index']);
```

En este punto cada ruta debería devolver en formato JSON los productos y categorías creados por el `DatabaseSeeder.php`.

Todas las rutas que se definen en el archivo `api.php` se generan por defecto bajo la ruta `/api`, por lo que tendrás que probar las siguientes rutas:
- http://localhost/api/categories
- http://localhost/api/products

Ahora que has comprobado que todo funciona bien, aplicaremos una pequeña mejora para dar la posibilidad de devolver los productos por categoría.

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index (Request $request)
    {
        // Filtrar los datos por categoria:
        if (request()->filled('category')) {
            return Product::where('category_id', '=', $request->category)->get();
        }
        // Si no se ha indicado una categoría, devolver todos:
        return Product::all();
    }
}
```

Puedes probar nuevamente la aplicación probando a filtrar los productos de la categoría con `id` igual a 1:
- http://localhost/api/products?category=1

### Crear la vista con los componentes de Vue

#### Instalar Bootstrap

Instala [Bootstrap 5](https://getbootstrap.com/) y sus dependencias:

```
npm i bootstrap sass @popperjs/core --save-dev
```
Nota: instalamos [Popper](https://popper.js.org/) ya que Bootstrap depende de la librería Popper para el comportamiento de elementos como dropdown, slider, popover etc.

Renombra el archivo `/app.css por resources/css/app.css` a `/app.css por resources/css/app.scss`

Abre el archivo ` vite.config.js` y actualiza la referencia para que apunte al archivo renombrado:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
```

Importa Bootstrap añadiendo la siguiente línea al archivo `resources/css/app.scss`:

```css
@import 'bootstrap/scss/bootstrap';
```

Añade las siguientes líneas al archivo `resources/css/app.js`:
```js
...
import * as Popper from '@popperjs/core'
window.Popper = Popper
import 'bootstrap'
...
```

Ejecuta el comando `npm run dev` en una nueva terminal.

Para probar que todo ha funcionado correctamente, crea una nueva vista llamada `home.blade.php` con el siguiente contenido:

```html
<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
</head>
<body>
<!-- Base from: https://startbootstrap.com/previews/blog-home -->

<!-- Not 100% Responsive navbar for simplification-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <!-- Logo zone -->
    <a class="navbar-brand" href="#!">Laravel + Vue.js Blog</a>

    <!-- Menu links -->
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
      <li class="nav-item"><a class="nav-link" href="#!">Inicio</a></li>
      <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
      <li class="nav-item"><a class="nav-link" href="#">View en Github</a></li>
    </ul>

  </div>
</nav>

<!-- Page content-->
<div class="container pt-5">
  <div class="row">
    <!-- Blog entries-->
    <div class="col-lg-8">

      <!-- Nested row for non-featured blog posts-->
      <div class="row">
        <div class="col-lg-6">
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla? Quos cum ex quis soluta, a laboriosam.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
        </div>
      </div>
      <!-- Pagination-->
      <nav aria-label="Pagination">
        <hr class="my-0" />
        <ul class="pagination justify-content-center my-4">
          <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Newer</a></li>
          <li class="page-item active" aria-current="page"><a class="page-link" href="#!">1</a></li>
          <li class="page-item"><a class="page-link" href="#!">2</a></li>
          <li class="page-item"><a class="page-link" href="#!">3</a></li>
          <li class="page-item disabled"><a class="page-link" href="#!">...</a></li>
          <li class="page-item"><a class="page-link" href="#!">15</a></li>
          <li class="page-item"><a class="page-link" href="#!">Older</a></li>
        </ul>
      </nav>
    </div>
    <!-- Side widgets-->
    <div class="col-lg-4">
      <!-- Categories widget-->
      <div class="card mb-4">
        <div class="card-header">Categories v2</div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Dashboard</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Shortcuts</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Overview</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Events</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Profile</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Status</a>
          </div>
        </div>
      </div>
      <!-- Categories widget-->
      <div class="card mb-4">
        <div class="card-header">Categories</div>
        <div class="card-body">
          <div class="row">
            <div class="col-sm-6">
              <ul class="list-unstyled mb-0">
                <li><a href="#!">Web Design</a></li>
                <li><a href="#!">HTML</a></li>
                <li><a href="#!">Freebies</a></li>
              </ul>
            </div>
            <div class="col-sm-6">
              <ul class="list-unstyled mb-0">
                <li><a href="#!">JavaScript</a></li>
                <li><a href="#!">CSS</a></li>
                <li><a href="#!">Tutorials</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- Side widget-->
      <div class="card mb-4">
        <div class="card-header">Side Widget</div>
        <div class="card-body">You can put anything you want inside of these side widgets. They are easy to use, and feature the Bootstrap 5 card component!</div>
      </div>
    </div>
  </div>
</div>
<!-- Footer-->
<footer class="py-5 bg-dark">
  <div class="container">
    <p class="m-0 text-center text-white">Copyright &copy; Your Website 2022</p>
  </div>
</footer>
</body>
</html>
```

#### Instalar Vue

https://laravel.com/docs/9.x/vite#vue

Instalar vue y el plugin de Vue para Vite:
```
npm install vue@latest
```
```
npm install --save-dev @vitejs/plugin-vue
```

También utilizaremos Axios para realizar las peticiones de información. Normalmente viene instalado con Laravel por defecto, pero si no lo estuviese (puedes comprobarlo en el archivo `package.json`) ejecuta el siguiente comando:
```
npm i vue-axios
```

Por último, actualiza el archivo `vite.config.js` añadiendo el plugin de Vue:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    // The Vue plugin will re-write asset URLs, when referenced
                    // in Single File Components, to point to the Laravel web
                    // server. Setting this to `null` allows the Laravel plugin
                    // to instead re-write asset URLs to point to the Vite
                    // server instead.
                    base: null,
 
                    // The Vue plugin will parse absolute URLs and treat them
                    // as absolute paths to files on disk. Setting this to
                    // `false` will leave absolute URLs un-touched so they can
                    // reference assets in the public directory as expected.
                    includeAbsolute: false,
                },
            },
        }),
        laravel({
            input: ['resources/css/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        },
    },
});

```

#### Crear los componentes de Vue

Crea una nueva carpeta en `resources/js` llamada `components`. En ella crearemos dos componentes:
- `Categories.vue`: cargarás las categorias desde nuestra API y las mostrará en un lateral de la pantalla.
- `ProudctList.vue`: cargará el listado de productos y los mostrará en la pantalla.

Empezaremos por crear el componente `Front.vue`, por lo que crearemos un nuevo archivo llamado `Front.vue` en el directorio `resources/js/components`. Los componentes de Vue tienen dos partes, <template> y <script>. De momento cogeremos el contenido principal de la vista `home.blade.php` que habíamos creado y lo copiaremos en `Front.vue`:

```html
<template>
<div class="container pt-5">
  <div class="row">
    <!-- Blog entries-->
    <div class="col-lg-8">

      <!-- Nested row for non-featured blog posts-->
      <div class="row">
        <div class="col-lg-6">
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">January 1, 2022</div>
              <h2 class="card-title h4">Post Title</h2>
              <p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Reiciendis aliquid atque, nulla? Quos cum ex quis soluta, a laboriosam.</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
        </div>
      </div>
      
    </div>
    <!-- Side widgets-->
    <div class="col-lg-4">
      <!-- Categories widget-->
      <div class="card mb-4">
        <div class="card-header">Categories</div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Category 1</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Category 2</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Category 3</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Category 4</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Category 5</a>
            <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Status</a>
          </div>
        </div>
      </div>
      
      <!-- Side widget-->
      <div class="card mb-4">
        <div class="card-header">Acerca de</div>
        <div class="card-body">Esta es una aplicación de ejemplo utilizando Laravel y Vue. Sigue aprendiendo más en <a href="https://laravel9.netlify.app">laravel9.netlify.app</div>
      </div>
    </div>
  </div>
</div>
</template>
```

El siguiente paso es decirle a nuestra aplicación que cargue el componente creado. En primer lugar, definiremos un `div` dentro de `home.blade.php` donde ubicaremos nuestro componente:

```html
<!-- Page content-->
<div id="app">
</div>
```

En segundo lugar, indicaremos a nuestra aplicación que cargue Vue, cree el componente y que lo cargue en el `div` que teníamos preparado. Para ello añadimos lo siguiente en el archivo ``resources/js/app.js`:

```js
import { createApp } from 'vue';
// import the root component App from a single-file component.
import App from './components/Front.vue';

const app = createApp(App);
app.mount('#app');
```

Vuelve a lanzar el comando ``npm run build` y carga tu página en el navegador. Deberás ver la página completa, la cual estará cargando el contenido principal utilizando Vuejs.

El siguiente paso es definir la parte del componente `Front.vue` donde se cargarán los datos del componente:

```js
<template>
...
</template>

<script>
    export default {
        data: function () {
            return {
                categories: [], // Listado de categorias
                products: [], // Listado de productos
            }
        },
        mounted() {
            this.loadCategories();
            this.loadProducts();
        },
        methods: {
            loadCategories: function () {
                axios.get('/api/categories')
                    .then((response) => {
                        this.categories = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            },
            loadProducts: function () {
                axios.get('/api/products')
                    .then((response) => {
                        this.products = response.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        }
    }
</script>
```

Ahora que ya tenemos definidos los datos del componente y la carga de los mismos mediante funciones, modificaremos la parte de la plantilla para que muestre los datos obtenidos desde la API:

```html
<template>
<template>
<div class="container pt-5">
  <div class="row">
    <!-- Blog entries-->
    <div class="col-lg-8">

      <!-- Nested row for non-featured blog posts-->
      <div class="row">
        <div class="col-lg-6" v-for="product in products">
          <!-- Blog post-->
          <div class="card mb-4">
            <a href="#!"><img class="card-img-top" src="https://dummyimage.com/700x350/dee2e6/6c757d.jpg" alt="..." /></a>
            <div class="card-body">
              <div class="small text-muted">{{ product.category }}</div>
              <h2 class="card-title h4">{{ product.name }}</h2>
              <p class="card-text">{{ product.description }}</p>
              <a class="btn btn-primary" href="#!">Read more →</a>
            </div>
          </div>
        </div>
        
      </div>
    </div>
    <!-- Side widgets-->
    <div class="col-lg-4">
      <!-- Categories widget-->
      <div class="card mb-4">
        <div class="card-header">Categories</div>
        <div class="card-body p-0">
          <div class="list-group list-group-flush">
            <a @click.stop.prevent="filterByCategory"  class="list-group-item list-group-item-action list-group-item-light p-3" 
                :href="category.id" v-for="category in categories">
                  {{ category.name }}
            </a>
          </div>
        </div>
      </div>
      
      <!-- Side widget-->
      <div class="card mb-4">
        <div class="card-header">Acerca de</div>
        <div class="card-body">Esta es una aplicación de ejemplo utilizando Laravel y Vue. Sigue aprendiendo más en <a href="https://laravel9.netlify.app">laravel9.netlify.app</div>
      </div>
    </div>
  </div>
</div>
</template>
</template>

<script>
  ...
</script>
```

Para probar los últimos cambios ejecuta:
```
npm run build
```

Vuelve a abrir la aplicación y verás la página con las categorías y productos devueltos por nuestros controladores.

Para añadir el filtrado por categorías, debemos añadir el evento `click` a los enlaces del menú de categorías mediante la directiva `@click`:

```html
<a  @click.stop.prevent="filterByCategory"
    class="list-group-item list-group-item-action list-group-item-light p-3" 
    :href="category.id"
    v-for="category in categories">
      {{ category.name }}
</a>
```

En este caso hemos añadido `.stop.propagation` para deter el comportamiento de navegación que tienen por defecto los enlaces. Es importante añadir el `id` de la categoría al atributo `href` ya que lo utilizaremos en la función `filterByCategory`. La implementación de la función será la siguiente:

```js
filterByCategory: function (event) {
    let categoria = event.currentTarget.getAttribute('href');
    axios.get(`/api/products?category=${categoria}`)
        .then((response) => {
            console.log(response.data);
            this.products = response.data;
        })
        .catch(function (error) {
            console.log(error);
        });
}
```

Vuelve a ejecutar ``npm run build` y prueba los cambios realizados.

## License

The code on this repository is open-sourced licensed under the [MIT license](https://opensource.org/licenses/MIT).
