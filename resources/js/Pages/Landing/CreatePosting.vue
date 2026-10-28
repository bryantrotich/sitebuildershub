<template>
    <LandingLayout>
        <Head title="Create Posting" />
        <template #breadcrumb>
            <ul class="crumb">
                <li><h4><a :href="route('landing.home')">Home</a></h4></li>
                <li><h4><a :href="route('landing.mypostings.create')">Create Posting</a></h4></li>
            </ul>
        </template>          
        <div class="container py-10">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-xl-9 col-lg-10">
                    <h2 class="font-size-7 text-center">Post Advert</h2>
                    <div class="bg-white px-9 pt-9 pb-7 shadow-8 rounded-4 mb-12">
                        <form action="/">
                                <div class="tab-content" id="myTabContent">
                                    <Transition name="slide-fade">
                                        <div class="tab-pane fade show active" v-if="$data.tab == 1" id="home" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="row">
                                                <div class="col-12 mb-4">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Location</label>
                                                    <LocationPicker
                                                        v-model="location"
                                                        :api-key="pageProps.maps.api_key"
                                                        :map-id="pageProps.maps.id"
                                                        :error="$data.errors.location ?? $data.errors.town"
                                                    />
                                                    <p v-show="has($data.errors,'location')" class="text-danger">{{ $data.errors.location }}</p>              
                                                    <p v-show="!has($data.errors,'location') && has($data.errors,'town')" class="text-danger">{{ $data.errors.town }}</p>              
                                                </div>                                
                                                <div class="col-12 mb-4">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Category</label>
                                                    <Multiselect 
                                                        :group-select="true"
                                                        group-values="sub_categories" 
                                                        group-label="name"
                                                        :options="categories"
                                                        :multiple="true"
                                                        track-by="name" 
                                                        label="name"
                                                        v-model="$data.active_form.categories"
                                                        @select="select_category"
                                                        @remove="deselect_category"
                                                    />                                                    
                                                    <p v-show="has($data.errors,'categories')" class="text-danger">{{ $data.errors.categories }}</p>              
                                                </div>
                                                <div class="col-12 mb-4">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Sub Category</label>
                                                    <Multiselect 
                                                        :options="sub_categories"
                                                        :multiple="true"
                                                        track-by="name" 
                                                        label="name"
                                                        v-model="$data.active_form.sub_categories"
                                                    />                                                    
                                                    <p v-show="has($data.errors,'categories')" class="text-danger">{{ $data.errors.sub_categories }}</p>              
                                                </div>                                                
                                                <div class="col-12">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Add Photo</label><br>
                                                    <label class="text-danger">Recommended image dimensions is 1080px by 720px</label>
                                                    <vue-dropzone
                                                        ref="images" 
                                                        id="images" 
                                                        :options="$data.photo_options"
                                                        @vdropzone-sending="addExtraFormData"
                                                        @vdropzone-success="successFileUpload"
                                                    />    
                                                    <p v-show="has($data.errors,'images')" class="text-danger">{{ $data.errors.images }}</p>              
                                                </div>
                                            </div>
                                        </div>
                                    </Transition>
                                    <Transition name="slide-fade">
                                        <div class="tab-pane fade show active" v-if="$data.tab == 2" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                            <div class="row">
                                                <div class="col-md-6 col-xs-12">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Title</label>
                                                    <input type="text" class="form-control" placeholder="Title" v-model="$data.active_form.title">
                                                    <p v-show="has($data.errors,'title')" class="text-danger">{{ $data.errors.title }}</p>              
                                                </div>
                                                <div class="col-md-6 col-xs-12">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Whatsapp Number</label>
                                                    <input type="text" class="form-control" placeholder="Phone Number" v-model="$data.active_form.phone_number">
                                                    <p v-show="has($data.errors,'phone_number')" class="text-danger">{{ $data.errors.phone_number }}</p>              
                                                </div>
                                                <div class="col-12">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Description</label>
                                                    <textarea type="text" class="form-control" placeholder="Description" rows="10" v-model="$data.active_form.description"></textarea>
                                                    <p v-show="has($data.errors,'description')" class="text-danger">{{ $data.errors.description }}</p>              
                                                </div>
                                                <div class="col-12">
                                                    <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Add Catalogue</label>
                                                    <vue-dropzone
                                                        ref="quotation" 
                                                        id="quotation" 
                                                        :options="$data.quotation_options"
                                                        @vdropzone-sending="addExtraFormData"
                                                        @vdropzone-success="successQuotationUpload"
                                                    />    
                                                    <p v-show="has($data.errors,'quotation')" class="text-danger">{{ $data.errors.quotation }}</p>              
                                                </div>                                                
                                            </div>
                                        </div>
                                    </Transition>
                                    <Transition name="slide-fade">
                                        <div class="tab-pane fade show active" v-if="$data.tab == 3" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                            <div class="row">                                            
                                                <div class="col-12 mb-2">
                                                    <label class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Promote Advert</label>
                                                    <div class="btn-group col-12 px-0">
                                                        <button 
                                                            type="button"
                                                            :class="`btn btn-primary btn-variants-outline ${$data.active_form.promotion_status ? 'active' : ''}`"
                                                            @click="$data.active_form.promotion_status = true"
                                                        >
                                                            Yes
                                                        </button>
                                                        <button 
                                                            type="button"
                                                            :class="`btn btn-primary btn-variant-outline ${!$data.active_form.promotion_status ? 'active' : ''}`"
                                                            @click="$data.active_form.promotion_status = false"
                                                        >
                                                            No
                                                        </button>
                                                    </div>  
                                                    <p v-show="has($data.errors,'promote.status')" class="text-danger">{{ $data.active_form.promotion_status }}</p>             
                                                </div> 
                                                <div class="col-12" v-if="$data.active_form.promotion_status">
                                                    <div class="row">
                                                        <div class="col-12 mb-2">
                                                            <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Placement Section</label>
                                                            <select class="form-control w-100" @change="select_section" :value="$data.active_form.promotion_section">
                                                                <option value="">Select Section*</option>
                                                                <option v-for="(item,key) in pageProps.placements" :value="item.section">{{ item.name }}</option> 
                                                            </select>
                                                            <p v-show="has($data.errors,'promotion_section')" class="text-danger">{{ $data.errors.promotion_section }}</p>              
                                                        </div>  
                                                        <div class="col-12">
                                                            <label for="" class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Add Photo</label>
                                                            <CFormInput type="file" id="formFile" v-if="isEmpty($data.active_form.promotion_image) && isEmpty($data.readers.advert_image)" @change="addPromotionImage"/>
                                                            <div class="col-12 px-0" v-if="!isEmpty($data.readers.advert_image)">
                                                                <VuePictureCropper                                
                                                                    :boxStyle="$data.crop_image.borderStyle"
                                                                    :img="$data.readers.advert_image"
                                                                    :options="$data.crop_image.options"
                                                                    :presetMode="$data.crop_image.presetMode"                                                                    
                                                                />  
                                                                <div class="col-12 d-flex justify-content-between my-2 px-0">
                                                                    <button type="button" class="btn btn-primary text-uppercase h-px-48" @click="selectCrop">Crop</button>          
                                                                    <button type="button" class="btn btn-danger text-uppercase h-px-48" @click="removeCrop">Cancel</button>          
                                                                </div>
                                                            </div>
                                                            <p class="font-size-4 font-weight-semibold text-danger" v-if="!isEmpty($data.selected_placement)">
                                                                Required Dimmensions. 
                                                                Width: {{ $data.selected_placement.custom.width }}px, Height: {{ $data.selected_placement.custom.height }}px
                                                            </p>
                                                            <div class="col-12 px-0" v-if="!isEmpty($data.readers.advert_crop_image) && !isEmpty($data.active_form.promotion_image)">
                                                                <picture>
                                                                    <img :src="$data.readers.advert_crop_image"/>
                                                                </picture>
                                                                <div class="col-12 d-flex justify-content-center my-2 px-0">
                                                                    <button type="button" class="btn btn-danger text-uppercase h-px-48" @click="deselectCrop">Remove</button>          
                                                                </div>                                                                
                                                            </div>
                                                            <p v-show="has($data.errors,'images')" class="text-danger">{{ $data.errors.images }}</p>              
                                                        </div>                                                        
                                                        <div class="col-12 mb-2"> 
                                                            <div class="row">  
                                                                <div class="col-6">
                                                                    <label class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Date From</label>
                                                                    <input type="date" :min="moment().format('YYYY-MM-DD')" class="form-control" placeholder="Date From" v-model="$data.active_form.promotion_date_from">
                                                                    <p v-show="has($data.errors,'promotion_date_from')" class="text-danger">{{ $data.errors.promotion_date_from }}</p>              
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Date To</label>
                                                                    <input type="date" :min="moment().format('YYYY-MM-DD')" class="form-control" placeholder="Date To" v-model="$data.active_form.promotion_date_to">
                                                                    <p v-show="has($data.errors,'promotion_date_to')" class="text-danger">{{ $data.errors.promotion_date_to }}</p>              
                                                                </div>  
                                                            </div>
                                                        </div> 
                                                        <div class="col-12">
                                                            <label class="font-size-4 font-weight-semibold text-black-2 mb-5 line-height-reset">Amount</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text p-2 bg-success text-white">Kshs</span>
                                                                </div>                                                            
                                                                <input type="number" class="form-control" placeholder="Cost" v-model="$data.active_form.promotion_amount">
                                                            </div>
                                                            <p v-show="has($data.errors,'promotion_amount')" class="text-danger">{{ $data.errors.promotion_amount }}</p>              
                                                        </div>                                                                                                            
                                                    </div>                                                     
                                                </div>                                               
                                            </div>
                                        </div>
                                    </Transition>
                                </div>
                            <div class="col-lg-12 mt-4 px-0 d-flex justify-content-between">
                                <button type="button" class="btn btn-primary text-uppercase h-px-48" v-if="$data.tab != 1 && $data.tab <= 3" @click="$data.tab--">Back</button>
                                <button type="button" class="btn btn-primary text-uppercase h-px-48" v-if="$data.tab < 3"  @click="nextTab" :disabled="$data.isDisabled">Next</button>
                                <button type="button" class="btn btn-primary text-uppercase h-px-48" v-if="$data.tab == 3" @click="submit" :disabled="$data.isDisabled">Create</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </LandingLayout>    
</template>
<style lang="css">
/*
  Enter and leave animations can use different
  durations and timing functions.
*/
.slide-fade-enter-active {
  transition: all 0.5s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.1s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateX(20px);
  opacity: 0;
}
</style>
<script lang="ts" setup>
import { LandingLayout } from '@/Layouts'
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';
import vueDropzone from 'dropzone-vue3';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import { object, string, number, date, InferType, array, mixed, boolean, ref } from 'yup';
import { isEmpty, has, cloneDeep, get, each, flatten, keys, set, flattenDeep, flattenDepth, add, pick } from 'lodash';
import moment from 'moment';
import VuePictureCropper, { cropper } from 'vue-picture-cropper'
import Multiselect from 'vue-multiselect'
import { LocationPicker } from '@/Components/Landing';

const $data: any  = reactive({
    active_form:   {},
    active_schema: {},
    photo_options: {
        addRemoveLinks: true,
        paramName:      'image',
        url:            route('landing.mypostings.image.upload'),
        method:         'post',
        acceptedFiles:  'image/*',
        // headers:        {'Content-Type': 'multipart/form-data'}
    },
    quotation_options: {
        paramName:      'quotation',
        url:            route('landing.mypostings.quotation.upload'),
        method:         'post',
        acceptedFiles:  'application/pdf',
        // headers:        {'Content-Type': 'multipart/form-data'}
    },    
    image_options: {
        paramName:       'image',
        url:             route('landing.mypostings.image.upload'),
        method:          'post',
        thumbnailHeight: 480,
        thumbnailWidth:  640,
        resizeWidth:     640,
        resizeHeight:    480,
        maxFilesize:     10.0,
    },
    selected_placement: {},
    selected_category:  "",
    errors: {},
    form:   {},
    modals: {
        crop_image: false
    },
    readers: {
        advert_image: "",
        advert_crop_image: ""
    },
    crop_image: {
        options: {
            viewMode: 1,
            dragMode: 'move',
            aspectRatio: 1,
            cropBoxResizable: false
        },
        borderStyle: {
            width: '100%',
            height: '100%',
            backgroundColor: '#f8f8f8',
            margin: 'auto',
        },
        presetMode: {
            mode: 'fixedSize',
            width: 200,
            height: 300,
        }
    },
    tab_forms: [
        {
            categories:   Array(),
            sub_categories:   Array(),
            location:     String(),     
            town:         String(),
            county:       String(),
            country:      String(),
            images:       Array(),
        },
        {
            title:        String(),
            description:  String(),
            quotation:    String(),
            phone_number: String()
        },
        {
            promotion_amount:       Number(),
            promotion_status:       Boolean(),
            promotion_section:      String(),
            promotion_image:        {},
            promotion_date_from:    moment().format('YYYY-MM-DD'),
            promotion_date_to:      moment().add(1,'days').format('YYYY-MM-DD'),
        }
    ],
    schemas: [
        {
            categories:   array().min(1,'*At least one category is needed.').required("*Category is required"),
            sub_categories:   array().min(1,'*At least one sub category is needed.').required("*Sub Category is required"),
            location:     string().required("*Location is required"),
            town:         string().required("*Please choose a more specific location, such as a town or city"),
            county:       string().required("*County could not be determined, please choose another location"),
            country:      string().required("*Country could not be determined, please choose another location"),
            images:       array().min(1,'*Please select at least one image').required("*Please select at least one image"),
        },
        {
            title:        string().required("*Title is required"),
            description:  string().required("*Description is required"),
            phone_number: string().required("*Phone Number is required"),
            quotation:    string().required("*Quotation is required"),
        },
        {
            promotion_amount: number().when("promotion_status",{
                is:   true,
                then: (schema) => schema.min(1, "*Promotion Cost cannot be 0" ).required("*Promotion Cost is required"),
                otherwise: (schema) => schema.nullable()
            }),
            promotion_image: string().when("promotion_status",{
                is:        true,
                then:      (schema) => schema.required("*Promotion Image is required"),
                otherwise: (schema) => schema.nullable()
            }),            
            promotion_date_from: string().when("promotion_status",{
                is:        true,
                then:      (schema) => schema.matches(/^\d{4}-\d{2}-\d{2}$/,'Promotion date from invalid').required("*Promotion Date From is required"),
                otherwise: (schema) => schema.nullable()
            }),
            promotion_date_to:   string().when("promotion_status",{
                is:        true,
                then:      (schema) => schema.matches(/^\d{4}-\d{2}-\d{2}$/,'Promotion date to invalid').required("*Promotion Date To is required"),
                otherwise: (schema) => schema.nullable()
            }),                                            
            promotion_section:   string().when("promotion_status",{
                is:        true,
                then:      (schema)      => schema.required("*Promotion Section is required"),
                otherwise: (schema) => schema.nullable()
            }),
            promotion_status:    boolean().required("*Promotion Status is required")  
        }
    ],
    tab: 1,
});


/**
 * Asynchronously reads the contents of a file as a data URL and returns it.
 *
 * @param {Object} target - The target input element containing the file to be read.
 * @return {Promise} A Promise that resolves to the data URL of the file.
 */
const getImageFile = (target: any) => {
    // Check if files are empty
    if( target.files.length == 0) return "";

    // Create a new FileReader instance
    return new Promise(resolve => {
        const reader = new FileReader()

        // Define the onload callback function
        reader.onload = function () {
            // Resolve the Promise with the data URL of the file
            resolve(reader.result)
        }

        // Read the contents of the file as a data URL
        reader.readAsDataURL(target.files[0])
    })        
}

const handleLocationSelect = () => {
    console.log(arguments);
}


const formSchema: any = computed( () => object().shape($data.active_schema) );

const select_category = (item: any) => {
    $data.selected_category = item.constructor == Array ? item[0].category_id : item.category_id
}

const deselect_category = (item: any) => {
    $data.selected_category = "";
}

/**
 * Validates a form field based on the provided field name.
 * Uses the formSchema to validate the field and updates the errors object accordingly.
 * Updates the isDisabled property based on the presence of errors.
 *
 * @param {string} field - The name of the field to validate.
 */
const validateForm = async (field:string) => {
    try {
        // Validate the field using the formSchema
        await formSchema.value.validateAt(field, $data.active_form);
		delete $data.errors[field];
    } catch(error: any) {
        // If the field is invalid, update the errors object with the error message
        $data.errors[error.path] = error.message;
    } finally {
        // Update the isDisabled property based on the presence of errors
        $data.isDisabled = !isEmpty($data.errors);
    }
}

/**
 * Calculates the aspect ratio of an image.
 *
 * The aspect ratio of an image is calculated by finding the greatest common divisor (GCD) of the width and height of the image.
 * The GCD is then used to divide the width and height of the image to find the aspect ratio.
 *
 * @param {number} width - The width of the image.
 * @param {number} height - The height of the image.
 * @returns {string} The aspect ratio of the image in the format "width:height".
 */
const aspectRatio = (width: number, height: number): any => {
  // Function to find the Greatest Common Divisor (GCD)
  const gcd = (a: number, b: number): number => b === 0 ? a : gcd(b, a % b);

  const commonDivisor     = gcd(width, height);
  const aspectRatioWidth  = width / commonDivisor;
  const aspectRatioHeight = height / commonDivisor;

  return `${aspectRatioWidth} / ${aspectRatioHeight}`;
}

const addPromotionImage = async ({ target }: any) => {
    $data.readers.advert_image = await getImageFile(target);
}

const categories: any     = computed( () => usePage().props.categories );

const sub_categories: any = computed( 
    () => {         
        return $data.selected_category == "" ? [] : categories.value.find( item => item.id == $data.selected_category).child_sub_categories
    }
);

// Bridges the location picker to the flat location/town/county/country form fields.
const location: any       = computed({
    get: () => pick($data.active_form, ['location','town','county','country']),
    set: (value: any) => Object.assign($data.active_form, pick(value, ['location','town','county','country'])),
});

const pageProps:  any     = computed( () => usePage().props );

const addExtraFormData = (file: any,xhr: any, formData: any) => {
    formData.append('_token', pageProps.value.csrf_token);
}

/**
 * Submits the login form.
 *
 * Posts the form data to the `login` route and resets the password field
 * on success.
 */
const submit = async () => {
    const section = pageProps.value.placements.find( (item:any) => item.section == $data.active_form.promotion_section );

    const form    = { 
        ...$data.form, 
        ...$data.active_form,
        _token: pageProps.value.csrf_token
    };

    if( !isEmpty(section) ){
        form['placement_id'] = section.id
    }

    useForm(form).post(
        route('landing.mypostings.store'), 
        {
            onSuccess: ({ props}: any) => {
                if( !form.promotion_status ){
                    if( !isEmpty(props.flash.message) ){
                        toast.success(props.flash.message);
                    }
                    resetForm();                    
                }

                console.log(props.data);
                
                if( has(props.data,'invoice') ){
                    window.location.href = route('landing.invoices.pay',{ invoice_number: props.data.invoice.invoice_number })                
                }
            },
        }
    );        
};

const create_invoice = async (promotion: any) => {
    router.put(
        route('landing.transactions.promotion.create',{ promotion: promotion.id }),
        {},
        {
            onSuccess: ({ props: { data: { order } } }: any) => {
                window.location.href = order.redirect_url;
            }
        }
    );
}

const calculate_promotion_cost = (value: any) => {
    const section = pageProps.value.placements.find( (item: any) => item.section == value );
    const days    = moment($data.active_form.promotion_date_to).diff(moment($data.active_form.promotion_date_from),'days');

    $data.active_form.promotion_section = value;
    $data.active_form.promotion_amount  = (section.price * days);
}

const removeCrop = () => {
    $data.readers.advert_crop_image   = "";
}

const deselectCrop = () => {
    $data.readers.advert_crop_image   = "";
    $data.readers.advert_image        = "";
    $data.active_form.promotion_image = {};
}


/**
 * Asynchronously selects a cropped image from the picture cropper and updates the form with its path.
 *
 * @return {Promise<void>} Resolves when the cropped image is selected and the form is updated.
 */
const selectCrop = async () => {
    try {
        console.log('Selecting crop...');
        // Check if the cropper is available
        if (!cropper) return;

        // Get the cropped image as a blob
        const blob: any = await cropper.getBlob();
        const reader    = new FileReader()        

        // Define the onload callback function
        reader.onload = function () {
            // Resolve the Promise with the data URL of the file
            $data.readers.advert_crop_image   = reader.result;
            $data.readers.advert_image        = "";
            $data.active_form.promotion_image = reader.result;        
        }

        // Read the contents of the file as a data URL
        reader.readAsDataURL(blob);
        
        console.log('Selecting crop...');

    } catch (error) {
        toast.error("An error occurred while processing the image. Please try again.");
        console.log(error);
        $data.crop = false;

        // Handle any errors that occur during the process
    }
}

const select_section = ($event:any) => {

    $data.active_form.promotion_date_from = moment().format('YYYY-MM-DD');
    $data.active_form.promotion_date_to   = moment().add(1,'days').format('YYYY-MM-DD');

    const value    = $event.target.value;
    const section  = pageProps.value.placements.find( (item: any) => item.section == value );
    const days     = moment($data.active_form.promotion_date_to).diff(moment($data.active_form.promotion_date_from),'days');

    $data.selected_placement             = cloneDeep(section);
    $data.crop_image.options.aspectRatio = aspectRatio(section.custom.width,section.custom.height);

    $data.crop_image.presetMode.height  = section.custom.height;
    $data.crop_image.presetMode.width   = section.custom.width;

    $data.image_options.thumbnailHeight = section.custom.height;
    $data.image_options.resizeHeight    = section.custom.height;
    $data.image_options.thumbnailWidth  = section.custom.width;
    $data.image_options.resizeWidth     = section.custom.width;

    $data.active_form.promotion_section = value;
    $data.active_form.promotion_amount  = (section.price * days);
}

/**
 * Called when a file is successfully uploaded.
 * 
 * @param {Object} _ - The file object.
 * @param {Object} { name } - The file name.
 */
const successFileUpload = (_: any, { name }: any) => {
    // Add the file name to the images array
    $data.active_form.images.push(name);
}

/**
 * Called when a file is successfully uploaded.
 * 
 * @param {Object} _ - The file object.
 * @param {Object} { name } - The file name.
 */
const successQuotationUpload = (_: any, { name }: any) => {
    console.log(name)
    // Add the file name to the images array
    $data.active_form.quotation = name;
}

/**
 * Called when a file is successfully uploaded.
 * 
 * @param {Object} _ - The file object.
 * @param {Object} { name } - The file name.
 */
const successPromotionImageUpload = (_: any, { name }: any) => {
    // Add the file name to the images array
    $data.active_form.promotion_image = name;
}

const nextTab = () => {
    $data.form = { ...$data.form, ...$data.active_form };
    $data.tab++;
}

/**
 * Resets the form data.
 *
 * Resets the email and password fields and clears the error state.
 */
const resetForm = () => {
    // Clear the error state
    $data.errors        = cloneDeep({});
    $data.active_form   = cloneDeep({});
    $data.active_schema = cloneDeep({});
    $data.form          = cloneDeep({});

    $data.tab_forms = [
        {
            category:     String(),
            location:     String(),     
            town:         String(),
            county:       String(),
            country:      String(),
            images:       Array(),
        },
        {
            title:        String(),
            description:  String(),
            phone_number: String()
        },
        {
            promotion_image:     String(),
            promotion_amount:    Number(),
            promotion_status:    Boolean(),
            promotion_section:   String(),
            promotion_date_from: moment().format('YYYY-MM-DD'),
            promotion_date_to:   moment().add(1,'days').format('YYYY-MM-DD'),
        }
    ]

    $data.tab           = 1;
}

/**
 * Watches for changes in the form data.
 *
 * Iterates over each field in the form and validates it using the validateForm function.
 * The watch is set to deep to ensure nested properties are observed.
 */
watch(
  () => $data.tab, 
  (tab) => {
    // Iterate over each field in the form and validate it
    $data.active_schema = $data.schemas[tab-1];
    $data.active_form   = $data.tab_forms[tab-1];
    $data.errors        = Object()
  },
  {
    immediate: true
  }
);

/**
 * Watches for changes in the form data.
 *
 * Iterates over each field in the form and validates it using the validateForm function.
 * The watch is set to deep to ensure nested properties are observed.
 */
 watch(
  () => $data.active_form, 
  (form: any) => {

    // Check on promotion form
    if( 
        !isEmpty(form.promotion_section) && 
        !isEmpty(form.promotion_date_from) &&
        !isEmpty(form.promotion_date_to)
    ){
        calculate_promotion_cost(form.promotion_section);
    }

    // Iterate over each field in the form and validate it
    each(
      form,
      (value, key) => {
        validateForm(key); // Validate the individual form field
      }
    );
  },
  { 
    deep: true, // Set to true to observe nested properties
    immediate: true
  }
);
</script>