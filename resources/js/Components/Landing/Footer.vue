<template>
  <!-- cta section -->
  <footer class="footer bg-ebony-clay dark-mode-texts">
    <div class="container  pt-12 pt-lg-19 pb-10 pb-lg-19">
      <div class="row">
        <div class="col-lg-4 col-sm-6 mb-lg-0 mb-9">
          <!-- footer logo start -->
          <a :href="route('landing.home')" class="d-inline-block mb-14">
            <img v-if="company?.logo" :src="company.logo" :alt="company.name" class="footer-logo">
            <span v-else class="font-size-6 font-weight-bold text-white">{{ company?.name }}</span>
          </a>
          <!-- footer logo End -->
          <!-- media start -->
          <div class="media mb-11">
            <img src="image/l1/png/message.png" class="align-self-center mr-3" alt="">
            <div class="media-body pl-5">
              <p class="mb-0 font-size-4 text-white">Contact us at</p>
              <a class="mb-0 font-size-4 font-weight-bold" href="mailto:support@uxtheme.net">info@sitebuildershub.com</a>
            </div>
          </div>
          <!-- media start -->
          <!-- widget social icon start -->
          <div class="social-icons">
            <ul class="pl-0 list-unstyled d-flex align-items-end ">
              <li class="d-flex flex-column justify-content-center px-3 mr-3 font-size-4 heading-default-color">Follow us on:</li>
              <li class="d-flex flex-column justify-content-center px-3 mr-3"><a href="#" class="hover-color-primary heading-default-color"><i class="fab fa-facebook-f font-size-3 pt-2"></i></a></li>
              <li class="d-flex flex-column justify-content-center px-3 mr-3"><a href="#" class="hover-color-primary heading-default-color"><i class="fab fa-twitter font-size-3 pt-2"></i></a></li>
              <li class="d-flex flex-column justify-content-center px-3 mr-3"><a href="#" class="hover-color-primary heading-default-color"><i class="fab fa-linkedin-in font-size-3 pt-2"></i></a></li>
            </ul>
          </div>
          <!-- widget social icon end -->
        </div>
        <div class="col-lg-8 col-md-6">
          <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-6 d-flex justify-content-between">
              <div class="footer-widget widget2 mb-md-0 mb-13">
                <!-- footer widget title start -->
                <p class="widget-title font-size-4 text-gray mb-md-8 mb-7">Company</p>
                <!-- footer widget title end -->
                <!-- widget social menu start -->
                <ul class="widget-links pl-0 list-unstyled list-hover-primary">
                  <li class="mb-6"><a class="heading-default-color font-size-4 font-weight-normal" href="">About us</a></li>
                  <li class="mb-6"><a class="heading-default-color font-size-4 font-weight-normal" href="">Contact us</a></li>
                  <li class="mb-6"><a class="heading-default-color font-size-4 font-weight-normal" href="">Careers</a></li>
                  <li class="mb-6"><a class="heading-default-color font-size-4 font-weight-normal" href="">Press</a></li>
                </ul>
                <!-- widget social menu end -->
              </div>
              <div class="footer-widget widget4">
                <!-- footer widget title start -->
                <p class="widget-title font-size-4 text-gray mb-md-8 mb-7">Legal</p>
                <!-- footer widget title end -->
                <ul class="widget-links pl-0 list-unstyled list-hover-primary">
                  <li class="mb-6"><a class="heading-default-color font-size-4 font-weight-normal" href="">Privacy Policy</a></li>
                  <li class="mb-6"><a class="heading-default-color font-size-4 font-weight-normal" href="">Terms & Conditions</a></li>
                  <li class="mb-6"><a class="heading-default-color font-size-4 font-weight-normal" href="">Return Policy</a></li>
                </ul>
              </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12" v-if="!isEmpty($data.placements)">
              <Swiper 
                slidesPerView="auto" 
                :spaceBetween="30" 
                :modules="$data.modules" 
                :loop="true" 
                :pagination="{clickable: false}"
                :centeredSlides="true"
                :autoplay="{delay: 2500,disableOnInteraction: true}"                
              >
                  <SwiperSlide v-for="(image,index) in advert_images" :key="index">
                      <img :src="image.url" :height="image.height" width="100%" />
                  </SwiperSlide>
              </Swiper>     
            </div>            
          </div>
        </div>
      </div>
    </div>
  </footer>
  <!-- footer area function end -->
</template>
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
  try {
    // Fetch the header data from the server.
    const { data:{ placements } } = await axios.get(route('landing.footer'));
    // Set the placements data on the component.
    $data.placements = placements;
  } catch (error) {
    // Log the error to the console.
    console.error('Error fetching data:', error);
  }
}

onMounted(fetch)
</script>