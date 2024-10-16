<template>
    <div class="flex flex-col h-screen max-h-screen p-2 md:p-4">
        <Header />
        <div class="flex flex-row gap-2 mt-2 grow" id="content-wrapper">
            <div
                class="relative flex flex-col max-h-full overflow-auto grow rounded-2xl bg-neutral-100 dark:bg-neutral-900 md:p-4 md:pr-0"
            >
                <router-view />
                <!-- btn hide sidebar -->
                <div
                    class="absolute flex items-center justify-center w-10 h-10 bg-blue-300 rounded-full bg-opacity-15 top-1 right-1 hover:bg-neutral-200 dark:hover:bg-neutral-800"
                    @click="this.open_info = !this.open_info"
                >
                    <i class="text-lg fa-regular fa-circle-info"></i>
                </div>
            </div>
            <div
                class="flex-col hidden max-h-full overflow-auto w-80 md:flex rounded-2xl bg-neutral-100 dark:bg-neutral-900 md:p-4 md:pr-0"
                v-if="open_info"
            >
                123
            </div>
        </div>
    </div>
    <Search :open="open_search" @update:open="updateOpen" />
</template>
<script>
import Header from "../components/Header.vue";
import Search from "../components/Search.vue";
export default {
    name: "Home Layout",
    components: {
        Header,
        Search,
    },
    data() {
        return {
            open_search: false,
            open_info: false,
            show_search: false,
            scroll_y: 0,
        };
    },
    methods: {
        updateOpen(open) {
            this.open_search = open;
        },
    },
};
</script>

<style lang="scss">
#content-wrapper {
    max-height: calc(100vh - 76px);
    max-width: calc(100vw - 16px);
    overflow: auto;
}
</style>
