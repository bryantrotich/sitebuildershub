<template>
    <LandingLayout>
        <Head title="Home" />
        <div class="position-relative z-index-0 dark-mode-texts" v-bind:class="{ 'bg-squeeze':isEmpty(banner_images) }">
            <div class="d-flex justify-content-between h-100" v-if="isEmpty(banner_images)">
                <div class="container d-flex align-items-center justify-content-center py-15 py-lg-23">
                    <div class="">
                        <div class="text-primary font-size-5 font-weight-semibold mb-7">
                        #{{ count_postings }} adverts posted right now
                        </div>
                        <h1 class="font-size-11 mb-9 text-black-2">Find what you are looking for ?</h1>
                        <p class="font-size-5">Here we give you the best construction products readily available for you to pick.</p>
                    </div>  
                </div>              
                <picture >
                    <img :src="placeholder_image" type="image/png"> 
                </picture>                   
            </div>   
            <Swiper 
                slidesPerView="auto" 
                :spaceBetween="30" 
                :modules="$data.modules" 
                :loop="true" 
                :pagination="{clickable: false}"
                :centeredSlides="true"
                :autoplay="{delay: 5000,disableOnInteraction: false}"  
                style="z-index: 0;"      
                v-if="!isEmpty(banner_images)"     
            >
                <SwiperSlide v-for="(image,index) in banner_images" :key="index">
                    <img :src="image.url" :height="image.height" width="100%" >
                </SwiperSlide>
            </Swiper>              
            <div class="container">
                <!-- Hero Form -->
                <div class="col-12 mx-auto" v-bind:class="{ 'translateY-50': !isEmpty(banner_images) }">
                    <form action="/" class="search-form">
                        <div class="filter-search-form-2 bg-white rounded-sm shadow-8 pr-8 py-7 pl-6">
                            <div class="filter-inputs">
                                <div class="form-group position-relative">
                                    <input class="form-control focus-reset pl-13" type="text" id="keyword" placeholder="Search by name, category or location">
                                    <span class="h-100 w-px-50 pos-abs-tl d-flex align-items-center justify-content-center font-size-6"><i class="icon icon-zoom-2 text-primary font-weight-bold"></i></span>
                                </div>
                                <!-- ./select-city ends -->
                            </div>
                            <div class="button-block">
                                <button class="btn btn-primary line-height-reset h-100 btn-submit w-100 text-uppercase">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- End Hero Form -->       
            </div>               
        </div>   
        <div class="col-12 px-0 pt-25" >
            <div class="container">
                <!-- Section Top -->
                <div class="row align-items-center justify-content-center">
                    <!-- Section Title -->
                    <div class="col-12 col-xl-6 col-lg-7 col-md-9">
                        <div class="text-center mb-12 mb-lg-17">
                            <h2 class="font-size-10 font-weight-bold mb-8">Explore by category</h2>
                        </div>
                    </div>
                </div>
                <!-- End Section Top -->
                <div class="row justify-content-center">
                    <!-- Single Category -->
                    <div class="col-12 col-xl-3 col-lg-4 col-sm-6 col-xs-8" v-for="(category,key) in $props.categories" :key="key">
                        <a href="#" class="bg-white border border-color-2 rounded-4 pl-5 pt-10 pb-3 px-2 hover-shadow-2 mb-9 d-block w-100 text-center">
                            <!-- Category Content -->
                            <div class="col-12">
                                <h5 class="font-size-5 font-weight-semibold text-black-2 line-height-1">{{ category.name }}</h5>
                                <p class="font-size-4 font-weight-normal text-gray"><span>{{ category.postings_count }}</span> Adverts</p>
                            </div>
                        </a>
                    </div>
                    <!-- End Single Category -->
                </div>
            </div>
        </div>

        <!-- Hero Area -->
        <!-- featuredJobOne Area -->
        <div class="pt-11 pt-lg-27 pb-7 pb-lg-26">
            <div class="container-fluid">
                <!-- Section Top -->
                <div class="row align-items-center pb-14">
                    <!-- Section Title -->
                    <div class="col-12 text-white text-center">
                        <h2 class="font-size-9 font-weight-bold">Trending Adverts</h2>
                        <h6>Browse through the most popular adverts on our platform. Find the best deals and offers from trusted sellers.</h6>
                    </div>
                </div>
                <!-- End Section Top -->
                <div class="row justify-content-center">
                    <div class="col-12" v-if="!isEmpty($props.postings)">
                        <Swiper                          
                            :slides-per-view="3"
                            :spaceBetween="0" 
                            :modules="$data.modules" 
                            :loop="true" 
                            :pagination="{clickable: false}"
                            :centeredSlides="true"
                            :autoplay="{delay: 2000,disableOnInteraction: false}"  
                            style="z-index: 0;"    
                            parallax       
                        >
                            <SwiperSlide v-for="(posting,index) in $props.postings" :key="index">
                                <a :href="route('landing.postings.view', { title: decodeURIComponent(posting.title) })" >
                                    <img :src="posting.link_images[0]" height="10%" width="90%">
                                </a>
                            </SwiperSlide>
                        </Swiper>                        
                    </div>
                </div>
            </div>
        </div>
        <!-- featuredJobOne Area --> 
        <!-- Hero Area -->         

    </LandingLayout>
</template>
<script lang="ts" setup>
import { LandingLayout } from '@/Layouts'
import { Head } from '@inertiajs/vue3';
import 'vue3-carousel/dist/carousel.css';
import { WhenVisible  } from '@inertiajs/vue3'
import { Carousel, Slide } from 'vue3-carousel';
import { computed, reactive } from 'vue';
// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import placeholder_image from '../../../images/globe-pattern.png';
import { Autoplay, Pagination, Navigation } from 'swiper/modules';
import { isEmpty } from 'lodash';
import { TrendingPosting } from '@/Components/Landing';

const $props: any = defineProps({
    categories:    Array,
    count_postings: Number,
    postings:      Array,
    placements:    Array,
    subscriptions: Array,
});

const $data: any   = reactive({
    slider: {
        current: 0,
    },
    modules: [Autoplay, Pagination, Navigation],
});

const banner_images = computed( 
    () => $props.placements
                .filter(     (item:any) => item.section == 'top-banner' )
                .map(        (item:any) => ({ ...item, promotions: item.promotions.map( (promotion:any) => ({ ...promotion, height: item.custom.height, width: item.custom.width  })) }) )
                .map(        (item:any) => item.promotions )
                .flat().map( (item:any) => ({ url: item.image, height: item.height, width: item.width }) )
);

const leader_images = computed( 
    () => $props.placements
                .filter(     (item:any) => item.section == 'leader-banner' )
                .map(        (item:any) => ({ ...item, promotions: item.promotions.map( (promotion:any) => ({ ...promotion, height: item.custom.height, width: item.custom.width })) }) )
                .map(        (item:any) => item.promotions )
                .flat().map( (item:any) => ({ url: item.image, height: item.height, width: item.width }) )                
);
</script>