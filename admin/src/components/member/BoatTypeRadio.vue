<script setup>
const model = defineModel();
const props = defineProps({
  boatTypes: Array,
});
</script>

<template>
  <fieldset class="sm:col-span-3">
    <legend class="text-sm font-semibold leading-6 text-gray-900">
      Type de bateau
    </legend>
    <div
      class="mt-6 space-y-6 sm:flex sm:items-center sm:space-x-10 sm:space-y-0"
    >
      <div
        v-for="type in props.boatTypes"
        :key="type.uid"
        class="flex items-center"
      >
        <input
          :id="type.uid"
          :name="type.uid"
          type="radio"
          class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"
          v-model="model"
          :value="type.uid"
        />
        <label
          :for="type.uid"
          class="ml-3 block text-sm font-medium leading-6 text-gray-900"
          >{{ type.name }}</label
        >
      </div>
    </div>
  </fieldset>
</template>
