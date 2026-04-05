<script setup>
defineProps({
  orderNumber: String,
  total: Number,
});

function formatPrice(n) {
  return new Intl.NumberFormat('fr-DZ').format(n) + ' DA';
}
</script>

<template>
  <Head :title="`Commande ${orderNumber} confirmée`" />
  <div class="min-h-screen bg-white flex items-center justify-center px-6">
    <div class="max-w-md w-full">

      <!-- Logo Kutch -->
      <div class="mb-10 flex items-center gap-3">
        <img src="/logo.png" alt="Kutch" class="h-8 w-auto" />
        <span class="text-base font-bold tracking-[0.22em] text-black uppercase">Kutch</span>
      </div>

      <h1 class="text-3xl font-bold uppercase tracking-tight text-black mb-3">
        Commande confirmée
      </h1>
      <p class="text-sm text-neutral-500 mb-10 leading-relaxed">
        Merci pour votre commande. Notre équipe vous contactera très prochainement pour confirmer la livraison.
      </p>

      <!-- Order summary box -->
      <div class="border border-neutral-200 p-6 mb-8">
        <div class="flex justify-between items-center py-3 border-b border-neutral-100">
          <span class="text-[10px] tracking-widest uppercase text-neutral-400">N° de commande</span>
          <span class="font-bold text-black text-sm">{{ orderNumber }}</span>
        </div>
        <div class="flex justify-between items-center py-3 border-b border-neutral-100">
          <span class="text-[10px] tracking-widest uppercase text-neutral-400">Total à payer</span>
          <span class="font-bold text-black text-sm">{{ formatPrice(total) }}</span>
        </div>
        <div class="flex justify-between items-center py-3">
          <span class="text-[10px] tracking-widest uppercase text-neutral-400">Paiement</span>
          <span class="text-[10px] font-bold tracking-widest uppercase text-black">À la livraison</span>
        </div>
      </div>

      <div class="flex flex-col gap-3">
        <a :href="`/suivi/${orderNumber}`"
          class="block w-full text-center bg-black text-white text-[11px] font-bold tracking-[0.2em] uppercase py-4 hover:bg-neutral-800 transition-colors">
          Suivre ma commande
        </a>
        <a href="/"
          class="block w-full text-center border border-neutral-300 text-black text-[11px] font-bold tracking-[0.2em] uppercase py-4 hover:border-black transition-colors">
          Retour à l'accueil
        </a>
      </div>
    </div>
  </div>
</template>
