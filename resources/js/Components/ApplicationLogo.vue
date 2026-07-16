<script setup>
import { usePage } from '@inertiajs/vue3';
const page = usePage();
</script>

<template>
    <img :src="`${page.props.appUrl}/logo.png`" alt="Kutch" />
</template>
