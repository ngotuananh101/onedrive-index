<template>
    <div class="flex w-full">
        <div class="search grow" id="search-header">
            <IconField class="font-semibold" v-if="this.scroll_y">
                <InputIcon class="pi pi-search" />
                <InputText
                    v-model="search"
                    placeholder="Tìm kiếm file..."
                    class="rounded-full w-full md:w-[40%] max-w-full border-none dark:bg-neutral-700 bg-neutral-100"
                />
            </IconField>
        </div>
        <div class="flex items-center">
            <Button
                v-for="(item, index) in this.listSocial"
                as="a"
                :href="item.url"
                :icon="item.icon"
                class="bg-transparent text-neutral-900 dark:text-white border-none hidden md:flex justify-center items-center"
                :key="index"
                target="_blank"
                rel="noopener"
            />
            <!-- Switch theme -->
            <Button
                :icon="this.darkMode ? 'pi pi-sun text-xl' : 'pi pi-moon text-xl'"
                class="bg-transparent text-neutral-900 dark:text-white border-none"
                @click="this.toggleDarkMode"
            />
        </div>
    </div>
</template>

<script>
import { useSystemConfigStore } from "../stores/systemConfigStore";
import { mapStores, mapActions, mapState } from "pinia";

export default {
    name: "App Header",
    data() {
        return {
            search: "",
            scroll_y: 0
        };
    },
    computed: {
        ...mapState(useSystemConfigStore, ['darkMode', 'listSocial']),
    },
    methods: {
        ...mapActions(useSystemConfigStore, ['toggleDarkMode']),
    },
    watch: {
        '$parent.$data.scroll_y': function(newValue) {
            this.scroll_y = newValue;
        }
    }
};
</script>
