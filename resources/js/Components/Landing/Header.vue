<template>
  <div>
    <!-- Header Area -->
    <header class="site-header site-header--menu-right bg-default text-white py-7 py-lg-0" >
      <div class="container">
        <nav class="navbar site-navbar offcanvas-active navbar-expand-lg  px-0 py-0">
          <!-- Brand Logo-->
          <div class="brand-logo">
            <a :href="route('landing.home')" class="d-flex align-items-center">
              <img v-if="company?.logo" :src="company.logo" :alt="company.name" class="site-logo">
              <span v-else class="font-size-6 font-weight-bold text-black-2">{{ company?.name }}</span>
            </a>
          </div>
          <div class="collapse navbar-collapse" id="mobile-menu">
            <div class="navbar-nav-wrapper">
              <ul class="navbar-nav main-menu">
                <li class="nav-item">
                  <a class="nav-link" :href="route('landing.home')">Home</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" :href="route('landing.postings')">Postings</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" :href="route('landing.projects')">Projects</a>
                </li>                
                <li class="nav-item">
                  <a class="nav-link" :href="route('landing.subscription')">Subscriptions</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" :href="route('landing.aboutus')">About Us</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" :href="route('landing.contactus')">Contact Us</a>
                </li>
              </ul>
            </div>
            <button class="d-block d-lg-none offcanvas-btn-close focus-reset" type="button" data-toggle="collapse" data-target="#mobile-menu" aria-controls="mobile-menu" aria-expanded="true" aria-label="Toggle navigation">
              <i class="gr-cross-icon"></i>
            </button>
          </div>
          <div class="header-btns header-btn-devider ml-auto pr-2 ml-lg-6 d-none d-xs-flex" v-if="isEmpty(auth_user)">
            <a class="btn btn-transparent text-uppercase font-size-3 heading-default-color focus-reset" href="#" @click="modals.login = true">
              Sign In
            </a>            
            <a class="btn btn-primary text-uppercase font-size-3" href="#" @click="modals.signup = true">
              Sign up
            </a>
          </div>
          <div class="header-btn-devider ml-auto ml-lg-5 pl-2 d-none d-xs-flex align-items-center" v-else>
            <a class="btn btn-primary" :href="route('landing.mypostings.create')">Create Posting</a>
            <!-- <div>
              <a href="#" class="px-3 ml-7 font-size-7 notification-block flex-y-center position-relative">
                <i class="fas fa-bell heading-default-color"></i>
                <span class="font-size-3 count font-weight-semibold text-white bg-primary circle-24 border border-width-3 border border-white">3</span>
              </a>
            </div> -->
            <div>
              <div class="dropdown show-gr-dropdown py-5">
                <a class="proile media ml-7 flex-y-center" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-user fa-lg mr-2"></i>   
                  {{ auth_user.name }}             
                  <!-- <div class="circle-40">
                  </div> -->
                  <i class="fas fa-chevron-down heading-default-color ml-6"></i>
                </a>
                <div class="dropdown-menu gr-menu-dropdown dropdown-right border-0 border-width-2 py-2 w-auto bg-default" aria-labelledby="dropdownMenuLink">
                  <a class="dropdown-item py-2 font-size-3 font-weight-semibold line-height-1p2 text-uppercase" :href="route('landing.mypostings')">My Postings </a>
                  <a class="dropdown-item py-2 font-size-3 font-weight-semibold line-height-1p2 text-uppercase" :href="route('landing.profile',{tab:'invoices'})">Invoices</a>
                  <a class="dropdown-item py-2 font-size-3 font-weight-semibold line-height-1p2 text-uppercase" :href="route('landing.profile',{ tab:'personal'})">Profile</a>
                  <a class="dropdown-item py-2 text-red font-size-3 font-weight-semibold line-height-1p2 text-uppercase" href="#" @click="logout">Log Out</a>
                </div>
              </div>
            </div>
          </div>
          <!-- Mobile Menu Hamburger-->
          <button class="navbar-toggler btn-close-off-canvas  hamburger-icon border-0" type="button" data-toggle="collapse" data-target="#mobile-menu" aria-controls="mobile-menu" aria-expanded="false" aria-label="Toggle navigation">
            <!-- <i class="icon icon-simple-remove icon-close"></i> -->
            <span class="hamburger hamburger--squeeze js-hamburger">
              <span class="hamburger-box">
                <span class="hamburger-inner"></span>
              </span>
            </span>
          </button>
          <!--/.Mobile Menu Hamburger Ends-->
        </nav>
      </div>
    </header>
    <div class="bg-default" v-if="!isEmpty($data.placements) && component != 'Landing/Home'">
      <Swiper 
          slidesPerView="auto" 
          :spaceBetween="30" 
          :modules="$data.modules" 
          :loop="advert_images.length > 1" 
          :pagination="{clickable: false}"
          :centeredSlides="true"
          :autoplay="{delay: 2500,disableOnInteraction: true}"                
      >
          <SwiperSlide v-for="(image,index) in advert_images" :key="index">
            <div :style="`width: 100%; height: ${image.height}px; background-image: url('${image.url}'); background-repeat: no-repeat; background-size: cover;`" ></div>
              <!-- <img :src="image.url" :height="image.height" width="100%" style="object-fit: cover;" /> -->
          </SwiperSlide>
      </Swiper>     
    </div>
  </div>
</template>
<style>
.swiper {
  z-index: 0;
}
</style>
<script setup>
import { isEmpty } from 'lodash'
import { router, usePage } from '@inertiajs/vue3'
import { reactive, computed, onMounted } from 'vue';
import axios from 'axios';
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
// import required modules
import { Autoplay, Pagination, Navigation } from 'swiper/modules';

const logout    = async () => router.post(route('landing.logout'));

/**
 * The computed property for the authenticated user.
 * 
 * @return {Object} - The authenticated user object.
 */
const auth_user = computed( () => usePage().props.auth.user )

/**
 * The company details (name, logo, icon) shared with every page.
 */
const company   = computed( () => usePage().props.company )

const advert_images = computed( 
    () => $data.placements
                .map(        (item) => ({ ...item, promotions: item.promotions.map( (promotion) => ({ ...promotion, height: item.custom.height, width: item.custom.width  })) }) )
                .map(        (item) => item.promotions )
                .flat().map( (item) => ({ url: item.image, height: item.height, width: item.width }) )
);

const $data  = reactive({
  modules: [Autoplay, Pagination, Navigation],
  placements: []
});

const $props = defineProps({
    modals: {
        type: Object,
        default: () => {}
    }
});

const component = computed(() => usePage().component );

const $emit = defineEmits(['update:modals']);

const modals = computed({
    get: ()      => $props.modals,
    set: (value) => $emit('update:modals', value),
});

/**
 * Fetch the header data from the server.
 * 
 * @return {Promise<void>} - A promise that resolves when the data has been fetched.
 */
const fetch = async () => {
  if(component.value === 'Landing/Home') return;
  try {
    // Fetch the header data from the server.
    const { data:{ placements } } = await axios.get(route('landing.header'));
    // Set the placements data on the component.
    $data.placements = placements;
  } catch (error) {
    // Log the error to the console.
    console.error('Error fetching data:', error);
  }
}

onMounted(fetch)
</script>
