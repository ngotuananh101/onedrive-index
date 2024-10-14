<template lang="">
  <div class="fixed inset-0 z-50 flex flex-col items-center justify-center w-screen
  h-screen text-black bg-neutral-100 dark:bg-neutral-900 dark:text-white" v-if="is_open" >
  <!-- Header --> <div class="w-full"> <div class="flex items-center justify-between
  w-full p-2"> <!-- Left --> <div class="max-w-[70%] flex items-center"> <button
  class="w-10 h-10 p-2 text-gray-500 rounded-full hover:bg-gray-200" @click="onClose" > <i
  class="fa-solid fa-xmark-large"></i> </button> <span class="block ml-3 truncate">{{
    name
  }}</span> </div> <!-- Right --> <div> <button class="w-10 h-10 p-2 text-gray-500
  rounded-full hover:bg-gray-200" @click="onDownload(this.download_url)" > <i
  class="fa-solid fa-download"></i> </button> </div> </div> </div> <!-- Preview iframe -->
  <iframe class="w-full h-full" :src="'https://docs.google.com/viewer?url=' + this.url +
  '&embedded=true'" > </iframe> </div>
</template>
<script>
export default {
  props: {
    is_open: Boolean,
    name: String,
    url: String,
    download_url: String,
    onClose: Function,
    onDownload: Function,
  },
  data() {
    return {};
  },
};
</script>
<style lang=""></style>
