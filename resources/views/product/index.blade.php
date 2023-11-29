@extends('adminlte::page')
@section('title',  $pageTitle)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{$pageTitle}}</h1>
    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Create Product</a>
</div>
@stop

@section('content')
<div class="container position-relative">
    <div class="row mb-3" v-if="products">
        <div class="col">
            <div class="card">
                <div class="card-body d-md-flex justify-content-between">
                    <div class="flex-fill align-self-end p-1">
                        <label>Keywords (Comma separated)</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Search product Name or Description. Use comma for multiple keywords" v-model="filter.keyword">
                    </div>
                    <div class="flex-fill align-self-end p-1">
                        <label>Category</label>
                        <select class="form-control form-control-sm" v-model="filter.category">
                            <option value="">-- All Category --</option>
                            <option v-for="item in productCategories" :value="item.id">@{{ item.name }}</option>
                        </select>
                    </div>
                    <div class="flex-fill align-self-end p-1">
                        <label>Sort By</label>
                        <select class="form-control form-control-sm" v-model="filter.sort_by">
                            <option value="id">ID</option>
                            <option value="name">Name</option>
                            <option value="category">Category</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                    <div class="flex-fill align-self-end p-1">
                        <label>Sort Order</label>
                        <select class="form-control form-control-sm" v-model="filter.sort_order">
                            <option value="ASC">Ascending</option>
                            <option value="DESC">Descending</option>
                        </select>
                    </div>
                    <div class="flex-fill align-self-end text-center p-1 d-flex justify-content-between">
                        <button class="btn btn-sm btn-primary flex-fill mr-1" @click="getProducts()">Search</button>
                        <button class="btn btn-sm btn-danger flex-fill" @click="resetFilters()">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row table-preloader"  v-if="isTableLoading">
        <div class="col p-5 text-center text-dark">
            <i class="fas fa-spin fa-circle-notch fa-3x"></i>
            <p>Loading records</p>
        </div>
    </div>
    <table class="table table-striped table-sm table-light table-bordered">
        <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Image</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Date</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="!products.length">
                <td colspan="6" class="text-center p-3 text-danger">No products found</td>
            </tr>
            <tr v-if="products.length" v-for="product in products">
                <td><a :href="product.routes.edit">@{{ product.id }}</a></td>
                <td class="text-center"><img :src="product.image" class="img-thumbnail" style="max-width: 74px;"></td>
                <td><a :href="product.routes.edit">@{{ product.name }}</a></td>
                <td>@{{ product.category.name }}</td>
                <td>@{{ product.date }}</td>
                <td class="text-center">
                    <div class="btn-group">
                        <a :href="product.routes.edit" class="btn btn-light btn-sm text-primary"><i class="fas fa-pen"></i></a>
                        <button class="btn btn-sm text-danger btn-light " @click="deleteProduct(product)"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot v-if="products.length">
            <tr>
                <td colspan="5">
                    <div class="input-group d-flex justify-content-center">
                        <div v-if="links.first" class="input-group-prepend">
                            <button :href="links.first" class="btn btn-sm btn-light btn-outline-light text-dark" @click="changePage($event, 1)">First</button>
                        </div>
                        <select class="form-control" @change="changePage($event)" v-model="current_page" style="max-width: 100px;">
                            <option v-for="n in meta.last_page" :value="n">
                                Page @{{ n }}
                            </option>
                        </select>
                        <div v-if="links.last" class="input-group-append">
                            <button :href="links.last" class="btn btn-sm btn-light btn-outline-light text-dark" @click="changePage($event, meta.last_page)">Last</button>
                        </div>
                    </div>
                </td>
            </td>
        </tfoot>
    </table>
</div>
@stop

@section('css')
<style>
.table-preloader{
    position: absolute;
    z-index: 999;
    background: #ffffffbd;
    width: 100%;
    height: 100%;
}
table{
    min-height: 100px;
    width:100%;
}
table td{
    width: 20%;
}
table td:first-child{
    width:10%;
    text-align: center;
}
table td:last-child{
    width:10%;
    text-align: center;
}
</style>
@stop
@section('js')
<script>
const { createApp } = Vue
vueTable = createApp({
    data() {
        return {
            isTableLoading : true,
            products : {},
            links: {},
            meta: {},
            current_page:1,
            productCategories:{},
            filter:{
                keyword:'',
                category:'',
                sort_order:'DESC',
                sort_by:'id',
            },
        }
    },
    methods : {
        resetFilters(){
            vueTable.filter = {
                keyword:'',
                category:'',
            };

            vueTable.getProducts()
        },

        getProductCategories(){
            axios.get(`{{ route('api.product-categories.data') }}`,{
                headers: {
                    'Authorization': 'Bearer {{ $apiToken }}'
                }
            })
            .then(response => {
                vueTable.productCategories = response.data.data;
            });
        },

        getProducts(){
            vueTable.isTableLoading = true;
            axios.get(`{{ route('api.products.data') }}?page=${vueTable.current_page}&sort_by=${vueTable.filter.sort_by}&sort_order=${vueTable.filter.sort_order}&keyword=${vueTable.filter.keyword}&category=${vueTable.filter.category}`,{
                headers: {
                    'Authorization': 'Bearer {{ $apiToken }}'
                }
            })
            .then(response => {
                vueTable.links = response.data.links;
                vueTable.meta = response.data.meta;
                vueTable.products = response.data.data;
                vueTable.current_page = response.data.meta.current_page;
                vueTable.isTableLoading = false;
            });
        },

        deleteProduct(product) {
            Swal.fire({
                text: `Are you sure you want to delete ${product.name} ?`,
                icon: "question",
                preConfirm:function(){
                    vueTable.isTableLoading = true;
                    axios.delete(product.routes.destroy,{
                        headers: {
                            'Authorization': 'Bearer {{ $apiToken }}'
                        }
                    })
                    .then(response => {
                        Toast.fire({
                            icon: response.data.type,
                            title: response.data.message
                        });
                        vueTable.getProducts();
                    });
                }
            });
        },

        changePage(event, page = null){
            if(page)
                vueTable.current_page = page;

            vueTable.getProducts();
        }

    }
}).mount('.content')
vueTable.getProducts();
vueTable.getProductCategories();

</script>
@stop
