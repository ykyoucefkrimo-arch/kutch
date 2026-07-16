<script setup>
import { reactive, ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';

const page = usePage();
const settings = computed(() => page.props.settings ?? {});
const footerImage = computed(() => page.props.footerImage);

// Réseaux sociaux configurables depuis Admin → Paramètres (icône masquée si le
// lien correspondant est vide, pour ne jamais pointer vers une page inexistante).
const socials = computed(() => [
  { key: 'instagram', url: settings.value.instagram, icon: 'instagram' },
  { key: 'facebook', url: settings.value.facebook, icon: 'facebook' },
  { key: 'linkedin', url: settings.value.linkedin, icon: 'linkedin' },
  { key: 'spotify', url: settings.value.spotify, icon: 'spotify' },
].filter(s => s.url));

const form = reactive({ name: '', email: '', phone: '', message: '' });
const sending = ref(false);
const sent = ref(false);
const error = ref('');

async function submit() {
  sending.value = true;
  error.value = '';
  try {
    await axios.post(route('contact.store'), form);
    sent.value = true;
    form.name = ''; form.email = ''; form.phone = ''; form.message = '';
    setTimeout(() => { sent.value = false; }, 5000);
  } catch (e) {
    error.value = e.response?.data?.message || 'Une erreur est survenue. Réessayez.';
  } finally {
    sending.value = false;
  }
}
</script>

<template>
  <footer id="contact" class="bg-white text-black mt-20 border-t border-neutral-200">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

      <!-- Formulaire de contact -->
      <div>
        <p class="text-[10px] font-bold tracking-[0.2em] uppercase text-neutral-400 mb-3">
          Agissez aujourd'hui pour un meilleur espace demain
        </p>
        <h2 class="text-2xl sm:text-3xl font-bold uppercase tracking-tight mb-6">
          Contact — Prenez un rendez-vous
        </h2>

        <p class="text-sm text-neutral-600 mb-6">
          Vous pouvez également nous écrire directement par mail :<br>
          <a v-if="settings.shop_email" :href="`mailto:${settings.shop_email}`" class="font-bold text-black hover:underline">{{ settings.shop_email }}</a>
          <span v-if="settings.shop_email && settings.shop_phone"> ou appelez-nous au </span>
          <a v-if="settings.shop_phone" :href="`tel:${settings.shop_phone.replace(/\s/g, '')}`" class="font-bold text-black hover:underline">{{ settings.shop_phone }}</a>.
        </p>
        <p v-if="settings.shop_address" class="text-sm font-bold text-black mb-8">{{ settings.shop_address }}</p>

        <form @submit.prevent="submit" class="space-y-5 max-w-md">
          <div>
            <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">Nom</label>
            <input v-model="form.name" type="text" required placeholder="Entrez votre nom complet"
              class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-black transition-colors" />
          </div>
          <div>
            <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">Email</label>
            <input v-model="form.email" type="email" required placeholder="Entrez votre adresse email"
              class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-black transition-colors" />
          </div>
          <div>
            <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">Téléphone</label>
            <input v-model="form.phone" type="tel" placeholder="Entrez votre numéro de téléphone"
              class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-black transition-colors" />
          </div>
          <div>
            <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">Message</label>
            <textarea v-model="form.message" required rows="4" placeholder="Décrivez-nous votre projet..."
              class="w-full border border-neutral-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:border-black transition-colors resize-none"></textarea>
          </div>

          <p v-if="error" class="text-red-500 text-xs">{{ error }}</p>
          <p v-if="sent" class="text-green-600 text-xs font-bold">✓ Message envoyé — nous vous répondrons rapidement.</p>

          <button type="submit" :disabled="sending"
            class="w-full bg-[#12172b] hover:bg-black disabled:opacity-50 text-white text-[11px] font-bold tracking-[0.2em] uppercase py-4 rounded-lg transition-colors">
            {{ sending ? 'Envoi...' : 'Prenez un rendez-vous' }}
          </button>
        </form>
      </div>

      <!-- Image (1er hero slide actif, configurable depuis Admin → Images Hero) -->
      <div v-if="footerImage" class="w-full aspect-[4/3] lg:aspect-auto lg:h-full rounded-2xl overflow-hidden bg-neutral-100">
        <img :src="footerImage" alt="" class="w-full h-full object-cover" />
      </div>
    </div>

    <!-- Réseaux sociaux -->
    <div v-if="socials.length" class="flex justify-center gap-4 pb-10">
      <a v-for="s in socials" :key="s.key" :href="s.url" target="_blank" rel="noopener noreferrer"
        class="w-10 h-10 rounded-full border border-neutral-300 flex items-center justify-center text-neutral-600 hover:text-black hover:border-black transition-colors">
        <svg v-if="s.icon === 'instagram'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="5" /><circle cx="12" cy="12" r="4" /><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none" />
        </svg>
        <svg v-else-if="s.icon === 'facebook'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M15 3h-2a5 5 0 0 0-5 5v2H6v4h2v7h4v-7h3l1-4h-4V8a1 1 0 0 1 1-1h3z" />
        </svg>
        <svg v-else-if="s.icon === 'linkedin'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="2" /><line x1="7" y1="10" x2="7" y2="16" /><circle cx="7" cy="7" r="0.5" fill="currentColor" />
          <path d="M11 16v-4a2 2 0 0 1 4 0v4" /><line x1="11" y1="10" x2="11" y2="16" />
        </svg>
        <svg v-else-if="s.icon === 'spotify'" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="12" cy="12" r="9" /><path d="M7.5 10c3-1 6.5-.5 9 1M8 13.5c2.5-.8 5-.4 7 .8M8.5 16.5c2-.6 4-.3 5.5.6" stroke-linecap="round" />
        </svg>
      </a>
    </div>

    <!-- Bottom bar -->
    <div class="border-t border-neutral-100 py-6">
      <p class="text-center text-[11px] tracking-wider text-neutral-400">
        © {{ new Date().getFullYear() }} {{ settings.shop_name || 'Kutch' }} — Tous droits réservés
      </p>
    </div>
  </footer>
</template>
