<script setup>
import Layout from "@/components/layouts/Layout.vue";
import EditorJS from "@editorjs/editorjs";
import axios from "axios";
import { ref, onMounted, reactive, watch } from "vue";
import { Switch, SwitchGroup, SwitchLabel } from "@headlessui/vue";

import { useRoute } from "vue-router";

const route = useRoute();
const loading = ref(true);
const editorRef = ref(null);
const refreshWatcher = ref(false);

let editor;

const scoopData = reactive({
  title: "",
  content: "",
  published: false,
});

const fetchScoopData = async () => {
  try {
    const { data } = await axios.get(`/api/scoop/${route.params.id}`);
    const { title, content, published } = data;

    scoopData.title = title;
    scoopData.content = content;
    scoopData.published = Boolean(published);

    console.log(data);
  } catch (error) {
    console.error("Erreur", error);
  } finally {
    loading.value = false;
  }
};

const patchScoop = async () => {
  try {
    await editor
      .save()
      .then((outputData) => {
        console.log("Article data: ", outputData);
        scoopData.content = outputData;
      })
      .catch((error) => {
        console.log("Saving failed: ", error);
      });

    const response = await axios.patch(`/api/scoop/${route.params.id}`, {
      title: scoopData.title,
      content: JSON.stringify(scoopData.content),
    });

    if (response.status === 200) {
      refreshWatcher.value = !refreshWatcher.value;
    }

    console.log("Mise à jour réussie :", response);
  } catch (error) {
    console.error("Erreur", error);
  }
};

const toggleVisibility = async () => {
  try {
    const response = await axios.get(
      `/api/scoop/${route.params.id}/toggle-visibility`,
    );

    if (response.status === 200) {
      refreshWatcher.value = !refreshWatcher.value;
    }

    console.log("Mise à jour réussie :", response);
  } catch (error) {
    console.error("Erreur", error);
  }
};

watch(
  () => refreshWatcher.value,
  () => fetchScoopData(),
);

onMounted(async () => {
  await fetchScoopData();
  editor = new EditorJS({
    holder: editorRef.value,
    data: JSON.parse(scoopData.content),
  });
});
</script>

<template>
  <Layout :loading>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <form class="mx-auto max-w-3xl" @submit.prevent="patchScoop">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
          <div class="col-span-full">
            <SwitchGroup as="div" class="flex items-center">
              <Switch
                v-model="scoopData.published"
                :class="[
                  scoopData.published ? 'bg-indigo-600' : 'bg-gray-200',
                  'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2',
                ]"
                @click="toggleVisibility"
              >
                <span
                  aria-hidden="true"
                  :class="[
                    scoopData.published ? 'translate-x-5' : 'translate-x-0',
                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out',
                  ]"
                />
              </Switch>
              <SwitchLabel as="span" class="ml-3 text-sm">
                <span class="font-medium text-gray-900">Publiée</span>
                {{ " " }}
                <span class="text-gray-500">(visible sur Internet)</span>
              </SwitchLabel>
            </SwitchGroup>
          </div>

          <div class="col-span-full">
            <label
              for="title"
              class="block text-sm font-medium leading-6 text-gray-900"
            >
              Titre
            </label>
            <div class="mt-2">
              <input
                type="text"
                name="title"
                id="title"
                autocomplete="title"
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                v-model="scoopData.title"
              />
            </div>
          </div>

          <div class="col-span-full">
            <div
              ref="editorRef"
              class="rounded-md py-10 shadow-sm ring-1 ring-inset ring-gray-300"
            ></div>
          </div>
        </div>
        <div class="mt-6 flex items-center justify-end gap-x-6">
          <button
            type="submit"
            class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
          >
            Sauvegarder
          </button>
        </div>
      </form>
    </div>
  </Layout>
</template>
