<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useCart } from '@/Composables/useCart';
import CartDrawer from './CartDrawer.vue';

const { count } = useCart();
const isAdmin = computed(() => !!usePage().props.auth?.user);

const isOpen  = ref(false);
const cartOpen = ref(false);
const scrolled = ref(window.scrollY > 0);

function onScroll() {
  scrolled.value = window.scrollY >= 40;
}

onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }));
onUnmounted(() => window.removeEventListener('scroll', onScroll));
</script>

<template>
  <header>
    <nav :class="{ 'bg-transparent': !scrolled && !isOpen, 'bg-white': scrolled || isOpen }"
         class="my-auto xl:px-4 fixed w-full inset-x-0 z-[100] py-4">

      <div class="flex duration-700 transition-all flex-wrap justify-between items-center mx-auto px-4 xl:px-0 xl:container">

        <!-- Logo -->
        <Link :href="route('home')" class="flex items-center ml-[13%]">
          <svg class="xl:h-10 h-8 xl:w-36 w-28 duration-300 transition-all"
               viewBox="0 0 200 40"
               xmlns="http://www.w3.org/2000/svg">
            <g>
              <path :class="{ 'fill-white': !scrolled && !isOpen, 'fill-black': scrolled || isOpen }"
                    class="duration-500 transition-all"
                    d="M61.35,9.71v32.81h-4.32l-.32-5.46-.02-.37-.19.32c-1.11,1.91-2.63,3.33-4.52,4.2-1.9.87-4.16,1.32-6.72,1.32-2.05,0-4.01-.39-5.82-1.16-1.81-.76-3.29-2.03-4.42-3.75-1.13-1.72-1.7-4-1.7-6.77V9.71h4.96v19.77c0,3.3.73,5.6,2.18,6.85,1.44,1.24,3.49,1.87,6.1,1.87,1.22,0,2.43-.21,3.6-.62,1.17-.41,2.24-1.07,3.18-1.97.94-.89,1.68-2.05,2.23-3.44.54-1.39.82-3.06.82-4.96V9.71h4.96"/>
              <path :class="{ 'fill-white': !scrolled && !isOpen, 'fill-black': scrolled || isOpen }"
                    class="duration-500 transition-all"
                    d="M127.86,0v15.52l.21-.36c1.1-1.91,2.59-3.31,4.42-4.16,1.83-.85,3.87-1.28,6.04-1.28,2.3,0,4.42.44,6.3,1.32,1.87.87,3.39,2.22,4.52,4.01,1.13,1.79,1.7,4.1,1.7,6.87v20.61h-4.96v-18.8c0-3.6-.79-6.14-2.34-7.56-1.55-1.42-3.48-2.13-5.74-2.13-1.56,0-3.15.37-4.71,1.11-1.57.74-2.88,1.96-3.9,3.64-1.02,1.67-1.54,3.92-1.54,6.7v17.05h-4.96V0h4.96"/>
              <path :class="{ 'fill-white': !scrolled && !isOpen, 'fill-black': scrolled || isOpen }"
                    class="duration-500 transition-all"
                    d="M104.57,9.07c.17,0,.35,0,.52,0,.11,0,.22,0,.32.01.18,0,.36.02.54.03.09,0,.18.01.27.02h.04c.05,0,.11,0,.17.02.07,0,.14.01.22.02h0s.07,0,.07,0h.03s.05,0,.05,0h0c.13.01.25.03.38.05.05,0,.11.01.16.02.15.02.3.05.45.08h.02s.01,0,.01,0c1,.19,1.95.47,2.82.84,1.34.57,2.55,1.33,3.6,2.28l.03.03c.15.14.29.28.44.42.06.06.12.12.19.19.11.11.2.21.29.31h0s0,0,0,0h0s.01.01.01.01c.15.17.29.34.43.5.85,1.06,1.58,2.27,2.16,3.61h0s.03.07.03.07l.04.08.03.07.04.09c.02.06.05.11.07.18l.04.1.02.06.03.07c.06.14.11.29.16.44v.04s.02,0,.02,0c.06.17.13.36.19.57.06.19.12.39.17.59l.03.11h-5.09c-.01-.06-.03-.12-.04-.17-.31-1.32-.85-2.47-1.61-3.41-.81-1-1.85-1.77-3.09-2.3-.69-.29-1.45-.5-2.27-.63-.52-.08-1.08-.13-1.65-.15-.16,0-.32,0-.49,0-2.17,0-4.07.5-5.63,1.47-.13.08-.26.17-.39.26l-.02.02-.06.05c-.08.05-.15.11-.22.16-.05.04-.1.08-.15.12-.14.11-.28.23-.42.36-.08.07-.15.14-.22.21l-.1.09-.03.03c-.08.08-.16.17-.24.25-.08.09-.16.18-.23.26-.06.07-.12.15-.19.23h-.02s-.03.06-.03.06c-.49.62-.92,1.35-1.29,2.15-.08.17-.15.35-.24.57-.09.22-.17.47-.26.75-.04.11-.07.22-.1.33-.21.72-.37,1.51-.48,2.35v.08s-.05.3-.05.3c-.02.15-.03.3-.04.44-.02.15-.02.3-.03.45,0,.12-.01.25-.02.39,0,.05,0,.1,0,.15,0,.28-.02.57-.02.87s0,.58.02.86v.07s0,.07,0,.07v.03c0,.13.01.25.02.37,0,.15.02.31.03.45,0,.15.02.3.04.45.01.13.03.26.05.39.11.83.27,1.62.48,2.34.03.11.06.22.1.33.08.27.17.52.26.75.08.22.16.41.24.57.37.81.8,1.54,1.3,2.17.07.09.14.19.22.27.07.09.15.18.23.26.07.08.15.17.23.24l.04.05.03.03s.09.09.12.11c.04.04.08.08.11.11.05.05.11.11.16.15.07.06.13.12.2.16.04.04.08.08.11.1.07.07.15.12.2.15.04.04.08.07.12.09.06.05.11.09.16.12.12.08.25.17.37.25,1.56.98,3.46,1.47,5.63,1.47.17,0,.33,0,.49,0,.57-.02,1.13-.07,1.65-.15.82-.13,1.58-.34,2.27-.63,1.24-.52,2.28-1.3,3.09-2.3.76-.95,1.3-2.09,1.61-3.41.02-.06.03-.12.04-.18h5.09l-.03.12c-.05.2-.11.39-.17.59-.07.21-.13.4-.2.59v.03c-.06.15-.12.29-.18.44-.02.07-.05.13-.08.19l-.02.04c-.02.06-.05.11-.07.17l-.04.09-.03.07-.04.08-.02.05c-.58,1.35-1.32,2.57-2.17,3.63-.13.16-.27.33-.43.5-.1.12-.21.23-.31.34-.06.07-.12.13-.19.19-.15.15-.3.29-.45.43h0s0,.01,0,.01h0c-1.05.95-2.27,1.72-3.61,2.29-.87.37-1.82.65-2.83.84l.02.11-.05-.1c-.18.03-.37.06-.56.09h-.05c-.12.03-.24.04-.36.05h-.04s-.04,0-.04,0c-.1.01-.21.02-.32.03h-.02s0,0,0,0h-.03s-.09.01-.09.01h-.02c-.1.01-.2.02-.31.03-.18.02-.36.03-.54.03-.11,0-.21,0-.32.01-.17,0-.35,0-.52,0-2.99,0-5.69-.67-8.04-1.99-.46-.26-.87-.52-1.27-.81-.3-.22-.56-.42-.79-.61-.27-.23-.51-.44-.74-.66-.47-.46-.92-.96-1.33-1.49-.19-.25-.39-.52-.6-.83-.29-.43-.56-.89-.8-1.35-.11-.21-.25-.49-.39-.79l-.1.05.09-.07-.02-.05c-.06-.12-.11-.25-.16-.38-.06-.14-.11-.28-.18-.44-.07-.17-.13-.35-.19-.53-.05-.13-.09-.27-.14-.42-.05-.15-.09-.31-.14-.46-.07-.23-.13-.49-.21-.82l-.05-.23v-.05c-.12-.51-.21-1.04-.29-1.57-.02-.14-.04-.27-.05-.42-.02-.13-.03-.27-.04-.41-.01-.11-.02-.22-.03-.34-.01-.11-.02-.23-.03-.35-.01-.17-.02-.33-.03-.5v-.05s0-.05,0-.05c0-.1,0-.21-.01-.32,0-.09,0-.19,0-.29,0-.17,0-.34,0-.51s0-.34,0-.51c0-.09,0-.18,0-.28,0-.11,0-.21.01-.32v-.05s0-.05,0-.05c0-.17.02-.33.03-.5,0-.11.02-.23.03-.35,0-.11.02-.23.03-.34,0-.14.02-.27.04-.41.02-.14.03-.28.05-.41.08-.62.19-1.22.33-1.8.02-.1.05-.2.07-.29.05-.19.1-.38.15-.57.04-.16.09-.31.14-.48.04-.14.09-.27.13-.4.06-.19.13-.37.2-.55.12-.29.24-.58.36-.86l-.1-.04.12.02c.05-.11.1-.22.16-.33l.03-.06v-.02s.1-.18.1-.18l.1-.19v-.02c.67-1.24,1.46-2.35,2.38-3.3.11-.12.23-.23.35-.35.22-.22.47-.44.74-.66.25-.21.51-.41.79-.61.4-.29.81-.55,1.27-.81.54-.31,1.12-.58,1.71-.82.2-.08.39-.15.59-.23.47-.17.95-.31,1.47-.44.33-.08.67-.15,1.02-.22,1.02-.19,2.11-.28,3.24-.28"/>
              <path :class="{ 'fill-white': !scrolled && !isOpen, 'fill-black': scrolled || isOpen }"
                    class="duration-500 transition-all"
                    d="M33.11,0l-16.84,17.25-.06.07.05.08,17.1,25.13h-5.95l-14.63-21.46-.08-.11-.09.1-7.61,7.78-.03.03v13.66H0V0h4.96v22.18l.19-.2L26.62,0h6.49"/>
              <path :class="{ 'fill-white': !scrolled && !isOpen, 'fill-black': scrolled || isOpen }"
                    class="duration-500 transition-all"
                    d="M76.91,0v9.71h9.85v4.25h-9.85v19.09c0,1.62.47,2.85,1.4,3.65.92.79,2.12,1.19,3.57,1.19,1.26,0,2.37-.22,3.29-.66.58-.28,1.15-.63,1.68-1.05l2.4,3.53c-.96.86-2.12,1.55-3.42,2.05-1.34.51-2.91.77-4.66.77-1.62,0-3.15-.29-4.56-.87-1.4-.57-2.52-1.49-3.35-2.74-.83-1.25-1.27-2.92-1.31-4.96V13.96h-6.48v-4.25h6.48V0h4.96"/>
            </g>
          </svg>
        </Link>

        <!-- Burger mobile -->
        <div>
          <button type="button" @click="isOpen = !isOpen" class="mr-4 xl:hidden">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path :class="{ 'fill-white': !scrolled && !isOpen, 'fill-black': scrolled || isOpen }"
                    d="M18.8889 17.5C19.1721 17.5004 19.4445 17.6223 19.6504 17.8411C19.8563 18.0598 19.9803 18.3587 19.9969 18.6767C20.0135 18.9948 19.9215 19.308 19.7397 19.5523C19.5579 19.7966 19.3001 19.9536 19.0189 19.9912L18.8889 20H1.11111C0.827912 19.9996 0.55552 19.8777 0.349591 19.6589C0.143662 19.4402 0.0197389 19.1413 0.00314209 18.8233C-0.0134547 18.5052 0.0785273 18.192 0.260294 17.9477C0.442061 17.7034 0.699893 17.5464 0.981111 17.5087L1.11111 17.5H18.8889ZM18.8889 8.75C19.1836 8.75 19.4662 8.8817 19.6746 9.11612C19.8829 9.35054 20 9.66848 20 10C20 10.3315 19.8829 10.6495 19.6746 10.8839C19.4662 11.1183 19.1836 11.25 18.8889 11.25H1.11111C0.816426 11.25 0.533811 11.1183 0.325437 10.8839C0.117063 10.6495 0 10.3315 0 10C0 9.66848 0.117063 9.35054 0.325437 9.11612C0.533811 8.8817 0.816426 8.75 1.11111 8.75H18.8889ZM18.8889 0C19.1836 0 19.4662 0.131696 19.6746 0.366117C19.8829 0.600537 20 0.918479 20 1.25C20 1.58152 19.8829 1.89946 19.6746 2.13388C19.4662 2.3683 19.1836 2.5 18.8889 2.5H1.11111C0.816426 2.5 0.533811 2.3683 0.325437 2.13388C0.117063 1.89946 0 1.58152 0 1.25C0 0.918479 0.117063 0.600537 0.325437 0.366117C0.533811 0.131696 0.816426 0 1.11111 0H18.8889Z"/>
            </svg>
          </button>
        </div>

        <!-- Menu mobile — v-show équivalent du x-show Alpine -->
        <nav v-show="isOpen" class="w-full xl:hidden" :class="{ 'hidden': !isOpen, 'block': isOpen }">
          <ul class="flex flex-col font-medium min-h-full my-16">
            <li class="px-6 mb-2">
              <Link href="#" @click="isOpen = false"
                    class="uppercase text-2xl font-extrabold text-black hover:no-underline hover:text-gray-600">
                Projets
              </Link>
            </li>
            <li class="px-6 mb-2">
              <Link :href="route('products.index')" @click="isOpen = false"
                    class="uppercase text-2xl font-extrabold text-black hover:no-underline hover:text-gray-600">
                Mobiliers
              </Link>
            </li>
            <li class="px-6 mb-2">
              <Link :href="route('products.index')" @click="isOpen = false"
                    class="uppercase text-2xl font-extrabold text-black hover:no-underline hover:text-gray-600">
                Boutique
              </Link>
            </li>
            <li class="px-6 mb-2">
              <Link href="#" @click="isOpen = false"
                    class="uppercase text-2xl font-extrabold text-black hover:no-underline hover:text-gray-600">
                Agence
              </Link>
            </li>
            <li class="px-6 mb-2">
              <Link href="#" @click="isOpen = false"
                    class="uppercase text-2xl font-extrabold text-black hover:no-underline hover:text-gray-600">
                Actualités
              </Link>
            </li>
            <li class="px-6 mb-2">
              <button @click="cartOpen = true; isOpen = false"
                      class="uppercase text-2xl font-extrabold text-black flex items-center gap-3">
                Panier <span v-if="count > 0">({{ count }})</span>
              </button>
            </li>
            <li v-if="isAdmin" class="px-6 mb-2">
              <Link href="/admin" @click="isOpen = false"
                    class="uppercase text-2xl font-extrabold text-black hover:no-underline hover:text-gray-600">
                Espace Admin
              </Link>
            </li>
          </ul>
        </nav>

        <!-- Desktop nav -->
        <div class="hidden justify-between w-full lg:flex lg:w-auto lg:order-1 items-center" id="mobile-menu-2">
          <ul class="flex flex-col items-center mt-4 lg:flex-row lg:space-x-12 lg:mt-0">
            <li>
              <a href="https://kutch.dz/projets"
                 class="text-xs font-black tracking-[0.15em] uppercase transition-colors duration-500" style="font-weight:900; -webkit-text-stroke: 0.4px currentColor"
                 :class="scrolled ? 'text-black hover:text-neutral-500' : 'text-white hover:text-white/70'">Projets</a>
            </li>
            <li>
              <a href="https://kutch.dz/catalogue/"
                 class="text-xs font-black tracking-[0.15em] uppercase transition-colors duration-500" style="font-weight:900; -webkit-text-stroke: 0.4px currentColor"
                 :class="scrolled ? 'text-black hover:text-neutral-500' : 'text-white hover:text-white/70'">Mobiliers</a>
            </li>
            <li>
              <Link :href="route('home')"
                    class="text-xs font-black tracking-[0.15em] uppercase transition-colors duration-500" style="font-weight:900; -webkit-text-stroke: 0.4px currentColor"
                    :class="scrolled ? 'text-black hover:text-neutral-500' : 'text-white hover:text-white/70'">Boutique</Link>
            </li>
            <li>
              <a href="https://kutch.dz/about/"
                 class="text-xs font-black tracking-[0.15em] uppercase transition-colors duration-500" style="font-weight:900; -webkit-text-stroke: 0.4px currentColor"
                 :class="scrolled ? 'text-black hover:text-neutral-500' : 'text-white hover:text-white/70'">Agence</a>
            </li>
            <li>
              <a href="https://kutch.dz/actualities"
                 class="text-xs font-black tracking-[0.15em] uppercase transition-colors duration-500" style="font-weight:900; -webkit-text-stroke: 0.4px currentColor"
                 :class="scrolled ? 'text-black hover:text-neutral-500' : 'text-white hover:text-white/70'">Actualités</a>
            </li>
            <!-- Panier -->
            <li>
              <button @click="cartOpen = true"
                      class="text-[11px] font-black tracking-[0.15em] uppercase transition-colors duration-500 relative"
                      :class="scrolled ? 'text-black hover:text-neutral-500' : 'text-white hover:text-white/70'">
                <svg class="w-[15px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <span v-if="count > 0"
                      class="absolute -top-1 -right-1.5 bg-black text-white text-[9px] rounded-full w-3.5 h-3.5 flex items-center justify-center font-bold leading-none">
                  {{ count }}
                </span>
              </button>
            </li>
          </ul>
        </div>

      </div>
    </nav>
  </header>

  <CartDrawer :open="cartOpen" @close="cartOpen = false" />
</template>

<style scoped>
.nav-link {
  @apply text-white text-[11px] font-extrabold tracking-[0.15em] uppercase transition-colors duration-500;
}
.nav-link:hover { @apply text-white/70; }

.nav-link-scrolled {
  @apply text-neutral-700 text-[11px] font-extrabold tracking-[0.15em] uppercase transition-colors duration-500;
}
.nav-link-scrolled:hover { @apply text-black; }
</style>
