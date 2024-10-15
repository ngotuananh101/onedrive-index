<template>
    <router-view></router-view>
    <!-- File preview -->
    <FilePreview
        :is_loading="this.filePreview.is_loading"
        :is_open="this.filePreview.is_open"
        :name="this.filePreview.name"
        :url="this.filePreview.url"
        :download_url="this.filePreview.download_url"
        :onClose="() => (this.filePreview.is_open = false)"
        :onDownload="download"
    />
    <Toaster />
</template>
<script>
import { mapStores } from "pinia";
import { useSystemConfigStore } from "./stores/systemConfigStore";
import { getPreviewUrl } from "./services/driveService";
import FilePreview from "./components/FilePreview.vue";
import Toaster from '@/components/ui/toast/Toaster.vue'
export default {
    name: "App",
    data() {
        return {
            filePreview: {
                is_open: false,
                name: "",
                url: "",
                download_url: "",
                is_loading: false,
            },
        };
    },
    components: {
        FilePreview,
        Toaster
    },
    computed: {
        ...mapStores(useSystemConfigStore),
    },
    mounted() {
        this.systemStore.init();
    },
    methods: {
        download(url) {
            window.open(url);
        },
        showPreview(file) {
            this.filePreview.is_loading = true;
            this.filePreview.is_open = true;
            this.filePreview.name = file.name;
            this.filePreview.download_url = file.download_url;
            getPreviewUrl(file.id).then((res) => {
                if (res.status === 200) {
                    this.filePreview.url = res.data.data;
                }
                this.filePreview.is_loading = false;
            });
        },
    },
};
</script>
