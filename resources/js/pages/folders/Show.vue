<template lang="">
  <div> </div> <FileTable :data="table_data" :columns="columns" :loading="loading"
  @onScrollEvent="onScrollEvent" @rowClick="rowClick" />
</template>

<script>
import FileTable from "../../components/FileTable.vue";
import { getFolderById } from "../../services/driveService";

export default {
  components: {
    FileTable,
  },
  data() {
    return {
      id: this.$route.params.id,
      scroll_y: 0,
      is_end: 0,
      loading: false,
      table_data: [],
      columns: [
        {
          name: "name",
          label: "Tên",
          class: "text-left font-semibold",
          has_icon: true,
        },
        {
          name: "created_by",
          label: "Chủ sở hữu",
        },
        {
          name: "modified",
          label: "Lần sửa đổi cuối",
        },
        {
          name: "size",
          label: "Kích cỡ tệp",
        },
      ],
      query: {
        per_page: 50,
        next_url: "",
      },
      total: 0,
    };
  },
  mounted() {
    this.fetchData();

    // Watch for route changes
    this.$watch("$route.params.id", (newId, oldId) => {
      if (newId !== oldId) {
        this.id = newId;
        this.fetchData();
      }
    });
  },
  methods: {
    fetchData() {
      this.loading = true;
      getFolderById(this.query, this.id).then((res) => {
        if (res.status === 200) {
          this.table_data = res.data.data;
          this.query.next_url = res.data.next_url;
          this.query.next_url ? (this.is_end = 1) : (this.is_end = 0);
        }
        this.loading = false;
      });
    },
    loadMore() {
      if (this.query.next_url && !this.loading && this.is_end === 1) {
        this.loading = true;
        getDriveRoot(this.query).then((res) => {
          if (res.status === 200) {
            this.table_data = [...this.table_data, ...res.data.data];
            this.query.next_url = res.data.next_url;
            this.query.next_url ? (this.is_end = 1) : (this.is_end = 0);
          }
          this.loading = false;
        });
      }
    },
    onScrollEvent(event) {
      this.scroll_y = event.srcElement.scrollTop;
      const viewportHeight = event.srcElement.clientHeight;
      if (this.scroll_y >= event.srcElement.scrollHeight - viewportHeight - 20) {
        this.is_end = 1;
      }
      this.loadMore();
    },
    rowClick(row) {
      if (row.type === "folder") {
        this.$router.push({
          name: "folders.show",
          params: { id: row.id },
        });
      }
    },
  },
};
</script>

<style lang=""></style>
