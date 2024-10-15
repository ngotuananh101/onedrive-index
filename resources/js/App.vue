<template>
  <router-view></router-view>
  <!-- File preview -->
  <FilePreview
    :is_open="this.filePreview.is_open"
    :name="this.filePreview.name"
    :url="this.filePreview.url"
    :download_url="this.filePreview.download_url"
    :onClose="() => (this.filePreview.is_open = false)"
    :onDownload="download"
  />
</template>
<script>
import { mapStores } from "pinia";
import { useSystemConfigStore } from "./stores/systemConfigStore";
import { getPreviewUrl } from "./services/driveService";
import FilePreview from "./components/FilePreview.vue";
export default {
  name: "App",
  data() {
    return {
      filePreview: {
        is_open: false,
        name: "",
        url: "",
        download_url: "",
      },
    };
  },
  components: {
    FilePreview,
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
      this.filePreview.is_open = true;
      this.filePreview.name = file.name;
      this.filePreview.url = file.download_url;
      this.filePreview.download_url = file.download_url;
    },
  },
};
</script>
