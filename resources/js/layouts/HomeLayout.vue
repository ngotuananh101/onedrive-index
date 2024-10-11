<template>
    <div class="flex p-2 md:p-4 h-screen max-h-screen">
        <div class="hidden md:block min-w-60">
            <div class="flex flex-col">
                <div class="flex items-center">
                    <img
                        src="/uploads/images/logo.png"
                        alt="Logo"
                        class="w-10 mr-3"
                    />
                    <span class="text-xl dark:text-neutral-300 font-medium">
                        OneDrive
                    </span>
                </div>
            </div>
        </div>
        <div class="flex flex-col content-between grow">
            <Header />
            <div
                class="grow flex mt-2 rounded-2xl bg-neutral-100 dark:bg-neutral-900 p-2 md:p-4 md:pr-1"
                id="content-wrapper"
            >
                <PerfectScrollbar
                    class="grow flex flex-col text-neutral-200"
                    @ps-scroll-y="onScrollEvent"
                >
                    <router-view />
                </PerfectScrollbar>
            </div>
        </div>
    </div>
</template>
<script>
import Header from "../components/Header.vue";
export default {
    name: "App Layout",
    components: {
        Header,
    },
    data() {
        return {
            scroll_y: 0,
        };
    },
    methods: {
        onScrollEvent(event) {
            this.scroll_y = event.srcElement.scrollTop;
        },
    },
};
</script>

<style lang="scss">
#content-wrapper {
    max-height: calc(100vh - 76px);
}
</style>
