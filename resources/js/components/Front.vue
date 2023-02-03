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
            <a @click.stop.prevent="filterByCategory"  class="list-group-item list-group-item-action list-group-item-light p-3" 
                :href="category.id" v-for="category in categories">
                  {{ category.name }}
            </a>
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
</template>

<script>
    export default {
        data: function () {
            return {
                categories: [],
                products: [],
                categoria: -1,
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
            },
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
        }
    }
</script>