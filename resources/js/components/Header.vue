<template>
  <div class="flex w-full text-black dark:text-white">
    <div class="flex items-center search grow" id="search-header">
      <button
        @click="openSearch"
        class="dark:bg-neutral-900 w-full md:w-[50%] lg:w-[30%] h-full rounded-full text-left bg-neutral-100"
      >
        <i class="ml-5 mr-2 fa-regular fa-magnifying-glass"></i>
        <span class="opacity-75">
          {{ $t("search") }}
        </span>
      </button>
    </div>
    <div class="flex items-center">
      <Button
        v-for="(item, index) in this.listSocial"
        variant="ghost"
        size="icon"
        :key="index"
        @click="openUrl(item)"
      >
        <i :class="item.icon"></i>
      </Button>
      <!-- Switch theme -->
      <Button variant="ghost" size="icon" @click="switchThemeMode">
        <i class="text-xl fa-solid fa-sun" v-if="themeMode === 'dark'"></i>
        <i class="text-xl fa-solid fa-moon" v-if="themeMode === 'light'"></i>
      </Button>
    </div>
  </div>
</template>

<script>
import { useSystemConfigStore } from "../stores/systemConfigStore";
import { mapActions, mapState } from "pinia";
import { Button } from "@/components/ui/button";

export default {
  name: "App Header",
  data() {
    return {
      scroll_y: 0,
    };
  },
  components: {
    Button,
  },
  mounted() {},
  computed: {
    ...mapState(useSystemConfigStore, ["themeMode", "listSocial"]),
  },
  methods: {
    ...mapActions(useSystemConfigStore, ["switchThemeMode"]),
    openUrl(item) {
      window.open(item.url);
    },
    openSearch() {
      this.$parent.open_search = true;
    },
  },
  watch: {
    "$parent.$data.scroll_y": function (newValue) {
      this.scroll_y = newValue;
    },
  },
};
</script>
