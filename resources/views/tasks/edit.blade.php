@extends('adminlte::page')
@section('title',  $pageTitle)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{$pageTitle}}</h1>
    <div>
    <a href="{{ route('tasks.index') }}" class="btn btn-primary btn-sm"><i class="fa fa-chevron-left"></i> Products List</a>
    @if ($task->id)
    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Create Product</a>
    @endif
    </div>
</div>
@stop

@section('content')
<div class="container position-relative">
    <div class="card" >
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Details</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Title</label>
                        <div class="col-sm-10">
                            <input class="form-control form-control-sm" v-bind:class="((!task.title && hasFormError) ? 'is-invalid':null )"type="text" v-model="task.title" name="name" required>
                            <div class="invalid-feedback">
                                Please provide a valid title.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Content</label>
                        <div class="col-sm-10">
                            <textarea class="form-control form-control-sm" type="text" v-model="task.content" id="content" name="content" v-bind:class="((!task.content && hasFormError) ? 'is-invalid':null )"></textarea>
                            <div class="invalid-feedback">
                                Please provide the content
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm" v-model="task.status" name="status" v-bind:class="((!task.status && hasFormError) ? 'is-invalid':null )">
                                <option value="to-do">To do</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            <div class="invalid-feedback">
                                Please provide the status
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Images</label>
                        <div class="col-sm-10">
                            <div class="row">
                                <div class="col">
                                    <div id="uppy-uploader"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3 text-center" v-if="task.images" v-for="image in task.images">
                                    <div class=" p-4 d-flex flex-column h-100">
                                        <a :href="image.link" data-fancybox :data-caption="image.title">
                                            <img class="img-thumbnail mb-1 align-self-start" :src="image.link" :alt="image.title"/>
                                        </a>
                                        <div class="btn-group mb-3">
                                            <button class="btn btn-light text-success" v-if="!image.is_primary" @click="setDefaultImage(image.routes.update)" title="Set as default image"><i class="fa fa-check"></i></button>
                                            <button class="btn btn-light text-danger" @click="deleteImage(image.routes.destroy)" title="Delete"><i class="fa fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('css')
<style>
.uppy-Dashboard-inner {
    margin: 0 auto;
}
.wizard{
    margin-bottom: 20px;
}
.wizard .btn {
    color: #272727;
    border-radius: 100%;
    border: 2px solid #b9b9b9;
    margin: 0 20px;
    background: #fff;
    z-index: 1;
    position: relative;
    line-height: 1;
    padding: 15px;
}
.wizard .btn.done {
    background: #d2ffd2;
    border-color: green;
}
.wizard .connector {
    position: absolute;
    border: 1px solid #b9b9b9;
    width: 100%;
    z-index: 0;
    top: 50%;
}
</style>
@stop
@section('js')
<script>
var uppyImages;
const axiosHeader = {
    headers: {'Authorization': 'Bearer {{ $apiToken }}'}
}
const { createApp } = Vue
vueTable = createApp({
    data() {
        return {
            isWaiting:true,
            hasFormError:false,
            task:{
                title:'',
                status:'',
                content:'',
                images:[],
            },
        }
    },
    methods : {
        deleteImage(route){
            Swal.fire({
                text: `Are you sure you want to delete this record?`,
                icon: "question",
                preConfirm:function(){
                    axios.delete(route,axiosHeader)
                    .then(response => {
                        vueTable.getTaskData();
                    });
                }
            });
        },
        setDefaultImage(route){
            console.log(route);
            Swal.fire({
                text: `Are you sure you want to set this as the default record image?`,
                icon: "question",
                preConfirm:function(){
                    axios.put(route,{
                        is_primary:1
                    },axiosHeader)
                    .then(response => {
                        vueTable.getTaskData();
                    });
                }
            });
        },
        getTaskData(){
            this.$data.isWaiting = true;
            axios.get(`{{ route('api.tasks.show',[$task->id]) }}`,axiosHeader)
            .then(response => {
                var data = response.data.data;
                this.$data.task.title = data.title;
                this.$data.task.status = data.status;
                this.$data.task.content = data.content;
                this.$data.task.images = data.images;
                tinymce.get('content').setContent(data.content);
                this.$data.isWaiting = false;
            });
        },

        save(){
            this.$data.isWaiting = true;
            this.$data.hasFormError = false;

            // save data
            @if ($task->id)
            axios.put(`{{ route('api.tasks.update',[$task->id]) }}`,this.$data.product, axiosHeader)
            @else
            axios.post(`{{ route('api.tasks.store') }}`,this.$data.product, axiosHeader)
            @endif
            .then(response => {
                uppyImages.getPlugin('XHRUpload').setOptions({
                    endpoint: response.data.data.routes.uploadImage,
                })

                uppyImages.upload().then((result) => {
                    console.info('Successful uploads:', result.successful);

                    if (result.failed.length > 0) {
                        console.error('Errors:');
                        result.failed.forEach((file) => {
                            console.error(file.error);
                        });
                    }

                    else{
                        Toast.fire({
                            icon: 'success',
                            title: `The record has been {{ ($task->id ? "updated":"created") }}`
                        });

                        window.setTimeout(() => {
                            window.location.replace(`{{ route('tasks.index') }}`);
                        }, 3000)

                    }

                });
            });
        },
        showErrors(wizardStep){
            this.$data.isWaiting = false;
            var stepIndex = wizardStep-1;
            this.$data.wizardErrors[stepIndex] = true;
            Toast.fire({
                icon: 'error',
                title: `Please complete step ${wizardStep}`
            });
            window.setTimeout(() => {
                this.$data.wizardErrors[stepIndex] = false;
            }, 3000)
            return false;
        },

    },
    mounted() {
        // initialize uploader
        uppyImages = new Uppy({
            restrictions:{
                maxFileSize:2097152, // 2 mb
                allowedFileTypes:['.jpg', '.jpeg', '.png']
            },
            logger: debugLogger,
            id:'uppyImages'
        })
        .use(Dashboard, {
            id :'dashImages',
            inline: true,
            target:'#uppy-uploader',
            height:'300px',
            wdith:'100%',
            locale: {
                strings: {
                    dropPasteFiles: '%{browseFiles}',
                    browseFiles: 'Select at least 1 product image',
                },
            },
            hideUploadButton:true,
        })
        .use(XHR, {
            endpoint: '/upload',
            method: 'POST',
            headers:{
                'Authorization': 'Bearer {{ $apiToken }}'
            }
        })
        .on('file-added', (file) => {
            return false;
            console.log('Added file', file);
        })
        .on('restriction-failed', (file, error) => {
            Toast.fire({
                icon: 'error',
                title: `An error occured while adding file. Make sure you are uploading an image file with maximum size of 2mb`
            });
        });
    }
}).mount('.content')

@if ($task->id)
vueTable.getTaskData()
@else
vueTable.isWaiting = false;
@endif

tinymce.init({
    selector: '#content',
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
    setup: function(editor) {
        editor.on('change', function () {
            editor.save();
            editor.getElement().dispatchEvent(new Event('input'));
        });
    }
});


</script>
@stop
