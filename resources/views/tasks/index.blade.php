@extends('adminlte::page')
@section('title',  $pageTitle)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{$pageTitle}}</h1>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Create Task</a>
</div>
@stop

@section('content')
<div class="container position-relative">
    <div class="row mb-3" v-if="tasks">
        <div class="col">
            <div class="card">
                <div class="card-body d-md-flex justify-content-between">
                    <div class="flex-fill align-self-end p-1">
                        <label>Keywords (Comma separated)</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Search tasks Name or Description. Use comma for multiple keywords" v-model="filter.keyword">
                    </div>
                    <div class="flex-fill align-self-end p-1">
                        <label>Status</label>
                        <select class="form-control form-control-sm" v-model="filter.status">
                            <option value="">-- All Status --</option>
                            <option value="to-do">To Do</option>
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
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
                        <button class="btn btn-sm btn-primary flex-fill mr-1" @click="getTasks()">Search</button>
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
                {{-- <th class="text-center">Image</th> --}}
                <th>Task Name</th>
                <th class="text-center">Status</th>
                <th>Content</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="!tasks.length">
                <td colspan="6" class="text-center p-3 text-danger">No tasks found</td>
            </tr>
            <tr v-if="tasks.length" v-for="tasks in tasks">
                <td><a :href="tasks.routes.edit">@{{ tasks.id }}</a></td>
                <td><a :href="tasks.routes.edit">@{{ tasks.title }}</a></td>
                <td v-html:="tasks.status_badge" class="text-center"></td>
                <td>@{{ tasks.content }}</td>
                <td class="text-center">
                    <div class="btn-group">
                        <a :href="tasks.routes.edit" class="btn btn-light btn-sm text-primary"><i class="fas fa-pen"></i></a>
                        <button class="btn btn-sm text-danger btn-light " @click="deleteTask(tasks)"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot v-if="tasks.length">
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
            tasks : {},
            links: {},
            meta: {},
            current_page:1,
            filter:{
                keyword:'',
                status:'',
                sort_order:'DESC',
                sort_by:'id',
            },
        }
    },
    methods : {
        resetFilters(){
            vueTable.filter = {
                keyword:'',
                status:'',
            };

            vueTable.getTasks()
        },
        getTasks(){
            vueTable.isTableLoading = true;
            axios.get(`{{ route('api.tasks.data') }}?page=${vueTable.current_page}&sort_by=${vueTable.filter.sort_by}&sort_order=${vueTable.filter.sort_order}&keyword=${vueTable.filter.keyword}&status=${vueTable.filter.status}`,{
                headers: {
                    'Authorization': 'Bearer {{ $apiToken }}'
                }
            })
            .then(response => {
                vueTable.links = response.data.links;
                vueTable.meta = response.data.meta;
                vueTable.tasks = response.data.data;
                vueTable.current_page = response.data.meta.current_page;
                vueTable.isTableLoading = false;
            });
        },

        deleteTask(tasks) {
            Swal.fire({
                text: `Are you sure you want to delete ${tasks.name} ?`,
                icon: "question",
                preConfirm:function(){
                    vueTable.isTableLoading = true;
                    axios.delete(tasks.routes.destroy,{
                        headers: {
                            'Authorization': 'Bearer {{ $apiToken }}'
                        }
                    })
                    .then(response => {
                        Toast.fire({
                            icon: response.data.type,
                            title: response.data.message
                        });
                        vueTable.getTasks();
                    });
                }
            });
        },

        changePage(event, page = null){
            if(page)
                vueTable.current_page = page;

            vueTable.getTasks();
        }

    }
}).mount('.content')
vueTable.getTasks();

</script>
@stop
