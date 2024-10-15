<script setup>
import { cn } from "/resources/js/lib/utils";

const props = defineProps({
  class: { type: null, required: false },
});
</script>

<template>
  <ol
    :class="
      cn(
        'flex flex-wrap items-center gap-1 break-words text-sm text-muted-foreground',
        props.class
      )
    "
  >
    <slot />
  </ol>
</template>
