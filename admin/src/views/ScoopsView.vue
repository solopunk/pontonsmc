<script setup>
import Layout from "@/components/layouts/Layout.vue";
import Table from "@/components/layouts/Table.vue";
import axios from "axios";
import { onMounted, ref, watch } from "vue";

const cells = [
  { title: "Date", key: "created_at" },
  { title: "Titre", key: "title" },
  { title: "Publié", key: "published" },
];

const scoops = ref([]);

const loading = ref(true);
const page = ref(1);
const hasMorePages = ref(false);
const fetchScoops = async () => {
  try {
    const response = await axios.get("/api/scoop", {
      params: { page: page.value },
    });
    scoops.value = response.data.data;
    hasMorePages.value = response.data.current_page < response.data.last_page;
  } catch (error) {
    console.error("Error", error);
  } finally {
    loading.value = false;
  }
};

// const nextPage = () => {
//   if (hasMorePages.value) {
//     page.value += 1;
//   }
// };

// const previousPage = () => {
//   if (page.value > 1) {
//     page.value -= 1;
//   }
// };

watch(page, fetchScoops);
onMounted(fetchScoops);
</script>

<template>
  <Layout :loading>
    <Table :cells :data="scoops" edit="scoop">
      <template #sub>Liste des actualités</template>
      <template #txt>
        Lorem ipsum dolor sit, amet consectetur adipisicing.
      </template>
      <template #btn>Ajouter une actualité</template>
    </Table>
    <button @click="nextPage">suivant</button>
  </Layout>
</template>
