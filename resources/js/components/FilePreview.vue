<template lang="">
    <div
        class="fixed inset-0 z-50 flex flex-col items-center justify-center w-screen h-screen text-black bg-neutral-100 dark:bg-neutral-900 dark:text-white"
        v-if="is_open"
    >
        <!-- Header -->
        <div class="w-full">
            <div class="flex items-center justify-between w-full p-2">
                <!-- Left -->
                <div class="max-w-[70%] flex items-center">
                    <button
                        class="w-10 h-10 p-2 text-gray-500 rounded-full hover:bg-gray-200"
                        @click="onClose"
                    >
                        <i class="fa-solid fa-xmark-large"></i>
                    </button>
                    <span class="block ml-3 truncate">{{ name }}</span>
                </div>
                <!-- Right -->
                <div>
                    <button
                        class="w-10 h-10 p-2 text-gray-500 rounded-full hover:bg-gray-200"
                        @click="onDownload(this.download_url)"
                    >
                        <i class="fa-solid fa-download"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Preview iframe -->
        <div class="w-full h-full" v-if="is_loading">
            <div class="flex flex-col items-center justify-center h-full">
                <div class="loading-wrapper">
                    <div class="blue ball"></div>
                    <div class="red ball"></div>
                    <div class="yellow ball"></div>
                    <div class="green ball"></div>
                </div>
                {{ $t("loading_preview") }}
            </div>
        </div>
        <iframe
            class="w-full h-full"
            :src="this.url"
            v-else-if="this.url"
        >
        </iframe>
        <div class="w-full h-full" v-else>
            <div class="flex flex-col items-center justify-center h-full">
                <div class="text-xl text-center">{{ $t("can_not_preview") }}</div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        is_loading: Boolean,
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
