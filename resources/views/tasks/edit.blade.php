@extends('adminlte::page')
@section('title',  $pageTitle)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1>{{$pageTitle}}</h1>
    <div>
    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm"><i class="fa fa-chevron-left"></i> Products List</a>
    @if ($product->id)
    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Create Product</a>
    @endif
    </div>
</div>
@stop

@section('content')
<div class="container position-relative">
    <div class="row mb-2">
        <div class="col text-center wizard">
            <div class="d-inline-block position-relative">
                <div class="connector"></div>
                <button v-for="n in 3" class="btn btn-large btn-border" v-bind:class="isWizardStepDone(n)">
                    Step <br>@{{ n }}<br>
                </button>
            </div>
        </div>
    </div>

    <div class="card" v-bind:class="isCurrentWizardStep(1)">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Details</h3>
                <div class="text-md-right">
                    <button class="btn btn-primary btn-sm" @click="changePage(2)" :disabled="isWaiting">Next Step <i class="fa fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input class="form-control form-control-sm" v-bind:class="((wizardErrors[0] && !product.name) ? 'is-invalid':null )"type="text" v-model="product.name" name="name" required>
                            <div class="invalid-feedback">
                                Please provide a valid name.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Category</label>
                        <div class="col-sm-10">
                            <select class="form-control form-control-sm" v-model="product.category_id" name="category_id" v-bind:class="((wizardErrors[0] && !product.category_id) ? 'is-invalid':null )">
                                <option v-for="item in productCategories" :value="item.id">@{{ item.name }}</option>
                            </select>
                            <div class="invalid-feedback">
                                Please provide a category
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Description</label>
                        <div class="col-sm-10">
                            <textarea class="form-control form-control-sm" type="text" v-model="product.description" id="description" name="description" v-bind:class="((wizardErrors[0] && !product.description) ? 'is-invalid':null )"></textarea>
                            <div class="invalid-feedback">
                                Please provide a description
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card" v-bind:class="isCurrentWizardStep(2)">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Images</h3>
                <div class="text-md-right">
                    <button class="btn btn-primary btn-sm" @click="changePage(1)" :disabled="isWaiting"><i class="fa fa-chevron-left"></i> Go Back</button>&nbsp;
                    <button class="btn btn-primary btn-sm" @click="changePage(3)" :disabled="isWaiting">Next Step <i class="fa fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="uppy-uploader"></div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-3 text-center" v-if="product.images" v-for="image in product.images">
                    <div class=" p-4 d-flex flex-column h-100">
                        <a :href="image.link" data-fancybox :data-caption="image.name">
                            <img class="img-thumbnail mb-1 align-self-start" :src="image.link" :alt="image.name"/>
                        </a>
                        <div class="btn-group mb-3">
                            <button class="btn btn-light text-success" v-if="!image.is_primary" @click="setDefaultImage(image.routes.update)" title="Set as default image"><i class="fa fa-check"></i></button>
                            <button class="btn btn-light text-danger" @click="deleteImage(image.routes.destroy)" title="Delete"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>

                </div>
            </div>
            <div id="uppy-uploader"></div>
        </div>
    </div>

    <div class="card" v-bind:class="isCurrentWizardStep(3)">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Date</h3>
                <div class="text-md-right">
                    <button class="btn btn-primary btn-sm" @click="changePage(2)" :disabled="isWaiting"><i class="fa fa-chevron-left"></i> Go Back</button>&nbsp;
                    <button class="btn btn-success btn-sm" @click="save()" :disabled="isWaiting">@{{ isWaiting ? 'Saving':'Save' }} <i class="fa fa-save"></i></button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Date</label>
                        <div class="col-sm-10">
                            <input class="form-control form-control-sm" v-bind:class="((wizardErrors[2] && !product.datetime) ? 'is-invalid':null )" type="text" v-model="product.datetime" id="datetime" name="datetime" readonly required>
                            <div class="invalid-feedback">
                                Please provide a valid date.
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
            wizardErrors:[false, false, false],
            wizardStep:1,
            productCategories:{},
            product:{
                name:'',
                category_id:'',
                description:'',
                images:[],
                datetime:'',
            },
        }
    },
    methods : {
        isWizardStepDone(index){
            if(index <= this.$data.wizardStep) return 'done';
        },
        deleteImage(route){
            Swal.fire({
                text: `Are you sure you want to delete this record ?`,
                icon: "question",
                preConfirm:function(){
                    axios.delete(route,axiosHeader)
                    .then(response => {
                        vueTable.getProductData();
                    });
                }
            });
        },
        setDefaultImage(route){
            console.log(route);
            Swal.fire({
                text: `Are you sure you want to set this as the default product image?`,
                icon: "question",
                preConfirm:function(){
                    axios.put(route,{
                        is_primary:1
                    },axiosHeader)
                    .then(response => {
                        vueTable.getProductData();
                    });
                }
            });
        },
        getProductData(){
            this.$data.isWaiting = true;
            axios.get(`{{ route('api.products.show',[$product->id]) }}`,axiosHeader)
            .then(response => {
                var data = response.data.data;
                this.$data.product.name = data.name;
                this.$data.product.category_id = data.category.id;
                this.$data.product.datetime = data.date;
                this.$data.product.description = data.description;
                this.$data.product.images = data.images;
                tinymce.get('description').setContent(data.description);
                this.$data.isWaiting = false;
            });
        },

        save(){
            this.$data.isWaiting = true;
            if(!this.validateWizardStep()){
                return false;
            }

            // save data
            @if ($product->id)
            axios.put(`{{ route('api.products.update',[$product->id]) }}`,this.$data.product, axiosHeader)
            @else
            axios.post(`{{ route('api.products.store') }}`,this.$data.product, axiosHeader)
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
                            title: `The record has been {{ ($product->id ? "updated":"created") }}`
                        });

                        window.setTimeout(() => {
                            window.location.replace(`{{ route('products.index') }}`);
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

        validateWizardStep(){
            for (let i = 1; i <= this.$data.wizardStep; i++) {
                if(
                    ((i==1) && (!this.$data.product.name || !this.$data.product.category_id || !this.$data.product.description )) ||
                    ((i==2) && (!this.$data.product.images.length && !uppyImages.getFiles().length )) ||
                    ((i==3) && (!this.$data.product.datetime))
                )
                    return this.showErrors(i);
            }
            return true;
        },
        isCurrentWizardStep(wizardStep){
            return (this.$data.wizardStep == wizardStep) ? null:'d-none';
        },
        changePage(wizardStep){
            if((wizardStep > this.$data.wizardStep) && !this.validateWizardStep())
                return false;
            this.$data.wizardStep = wizardStep;
        },

        getProductCategories(){
            axios.get(`{{ route('api.product-categories.data') }}`,axiosHeader)
            .then(response => {
                vueTable.productCategories = response.data.data;
            });
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

        // initialize datepicker
        $('#datetime').daterangepicker({
            "singleDatePicker": true,
            "showDropdowns": true,
            "timePicker": true,
            "autoApply": true,
            locale: {
                format: 'YYYY-MM-DD hh:mm A'
            }
        });

        $('body').on('change','#datetime', function(){
            vueTable.product.datetime = $(this).val();
            console.log(vueTable.product);
        })

    }
}).mount('.content')

@if ($product->id)
vueTable.getProductData()
@else
vueTable.isWaiting = false;
@endif
vueTable.getProductCategories();

tinymce.init({
    selector: '#description',
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
