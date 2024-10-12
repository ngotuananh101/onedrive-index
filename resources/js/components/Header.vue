<template>
    <div class="flex w-full text-black dark:text-white">
        <div class="search grow" id="search-header">
            <div class="relative items-center w-full max-w-sm">
                <Input
                    id="search"
                    type="text"
                    :placeholder="$t('search')"
                    class="pl-10 rounded-full"
                    v-model="this.$parent.search"
                />
                <span
                    class="absolute inset-y-0 flex items-center justify-center px-2 start-2"
                >
                    <i class="fa-regular fa-magnifying-glass"></i>
                </span>
                <span
                    class="absolute inset-y-0 flex items-center justify-center px-2 end-2"
                    @click="this.$parent.search = ''"
                    v-if="this.$parent.search"
                >
                    <i class="fa-solid fa-circle-xmark"></i>
                </span>
            </div>
        </div>
        <div class="flex items-center">
            <Button
                v-for="(item, index) in this.listSocial"
                variant="ghost"
                size="icon"
                :key="index"
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
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

export default {
    name: "App Header",
    components: {
        Input,
        Button,
    },
    data() {
        return {
            scroll_y: 0,
        };
    },
    mounted() {
    },
    computed: {
        ...mapState(useSystemConfigStore, ["themeMode", "listSocial"]),
    },
    methods: {
        ...mapActions(useSystemConfigStore, ["switchThemeMode"]),
    },
    watch: {
        "$parent.$data.scroll_y": function (newValue) {
            this.scroll_y = newValue;
        },
    },
};
</script>
