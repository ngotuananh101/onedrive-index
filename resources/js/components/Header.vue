<template>
    <div class="flex justify-between w-full text-black dark:text-white">
        <router-link :to="{ name: 'index' }" class="flex items-center">
            <img src="/uploads/images/logo.png" alt="Logo" class="w-10 mr-3" />
            <span class="hidden text-xl font-medium dark:text-neutral-300 md:block">
                {{ $t("app_name") }}
            </span>
        </router-link>
        <div class="flex items-center justify-end search grow" id="search-header" v-if="this.$parent.$data.scroll_y || this.$parent.$data.show_search">
            <button
                @click="openSearch"
                class="w-full h-full text-left md:w-[50%] lg:w-[30%] rounded-full dark:bg-neutral-900 bg-neutral-100"
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
                <i
                    class="text-xl fa-solid fa-sun"
                    v-if="themeMode === 'dark'"
                ></i>
                <i
                    class="text-xl fa-solid fa-moon"
                    v-if="themeMode === 'light'"
                ></i>
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
};
</script>
