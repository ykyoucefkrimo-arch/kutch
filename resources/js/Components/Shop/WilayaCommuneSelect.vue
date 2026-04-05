<script setup>
import { watch, onMounted } from 'vue';
import { useLocations } from '@/Composables/useLocations';

const props = defineProps({
  modelWilaya: Number,
  modelCommune: Number,
  errorWilaya: String,
  errorCommune: String,
});

const emit = defineEmits(['update:modelWilaya', 'update:modelCommune']);

const { wilayas, communes, loadingWilayas, loadingCommunes, fetchWilayas, fetchCommunes } = useLocations();

onMounted(() => fetchWilayas());

watch(() => props.modelWilaya, (val) => {
  emit('update:modelCommune', null);
  if (val) fetchCommunes(val);
});
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <!-- Wilaya -->
    <div>
      <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">Wilaya *</label>
      <select
        :value="modelWilaya"
        @change="emit('update:modelWilaya', parseInt($event.target.value) || null)"
        class="w-full border-b border-neutral-300 py-2.5 focus:outline-none focus:border-black bg-transparent text-sm transition-colors"
        :class="{ 'border-red-500': errorWilaya }"
      >
        <option :value="null">{{ loadingWilayas ? 'Chargement...' : '— Sélectionner une wilaya' }}</option>
        <option v-for="w in wilayas" :key="w.id" :value="w.id">
          {{ w.code }}. {{ w.name }}
        </option>
      </select>
      <p v-if="errorWilaya" class="text-red-500 text-xs mt-1">{{ errorWilaya }}</p>
    </div>

    <!-- Commune -->
    <div>
      <label class="block text-[10px] tracking-[0.18em] uppercase font-semibold text-neutral-500 mb-2">Commune *</label>
      <select
        :value="modelCommune"
        @change="emit('update:modelCommune', parseInt($event.target.value) || null)"
        :disabled="!modelWilaya || loadingCommunes"
        class="w-full border-b border-neutral-300 py-2.5 focus:outline-none focus:border-black bg-transparent text-sm transition-colors disabled:opacity-40"
        :class="{ 'border-red-500': errorCommune }"
      >
        <option :value="null">{{ loadingCommunes ? 'Chargement...' : '— Sélectionner une commune' }}</option>
        <option v-for="c in communes" :key="c.id" :value="c.id">{{ c.name }}</option>
      </select>
      <p v-if="errorCommune" class="text-red-500 text-xs mt-1">{{ errorCommune }}</p>
    </div>
  </div>
</template>
