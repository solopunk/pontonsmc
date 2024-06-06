<script setup>
import Layout from "@/components/layouts/Layout.vue";
import InputComp from "@/components/member/InputComp.vue";
import { ref, onMounted, reactive, watch } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import FormCard from "@/components/member/FormCard.vue";
import MemberTypeRadio from "@/components/member/MemberTypeRadio.vue";
import BoatTypeRadio from "@/components/member/BoatTypeRadio.vue";
import HomeportRadio from "@/components/member/HomeportRadio.vue";
import Coowner from "@/components/member/Coowner.vue";

const route = useRoute();
const loading = ref(true);
const refreshWatcher = ref(false);

const radioPossibilities = reactive({
  memberTypes: [],
  boatTypes: [],
  homeports: [],
});

const fetchData = async () => {
  try {
    const { data } = await axios.get(`/api/member/${route.params.id}`);
    const {
      type,
      email,
      first,
      last,
      birthdate,
      address,
      postal_code,
      city,
      phone,
      job,
      boat,
      hasCoowner,
      coowner,
    } = data;

    inputs.type = type.uid;
    inputs.member = {
      email,
      first,
      last,
      birthdate,
      address,
      postal_code,
      city,
      phone,
      job,
    };
    inputs.boat = boat || {
      name: "",
      brand: "",
      model: "",
      year: "",
      length: 0,
      width: 0,
      type: "engine",
      homeport: "hercule",
    };
    inputs.hasCoowner = hasCoowner;
    inputs.coowner = coowner || {
      first: "",
      last: "",
      nationality: "",
    };

    console.log(data);

    radioPossibilities.memberTypes = data.memberTypes;
    radioPossibilities.boatTypes = data.boatTypes;
    radioPossibilities.homeports = data.homeports;
  } catch (error) {
    console.error("Erreur lors de la récupération du membre :", error);
  } finally {
    loading.value = false;
  }
};

const inputs = reactive({
  type: "",
  member: {
    email: "",
    first: "",
    last: "",
    birthdate: "",
    address: "",
    postal_code: "",
    city: "",
    phone: "",
    job: "",
  },
  boat: {
    name: "",
    brand: "",
    model: "",
    year: "",
    length: 0,
    width: 0,
    type: "",
    homeport: "",
  },
  hasCoowner: false,
  coowner: {
    first: "",
    last: "",
    nationality: "",
  },
});

const patchMember = async () => {
  try {
    let request = {};
    request.member = inputs.member;

    const response = await axios.patch(
      `/api/member/${route.params.id}`,
      request,
    );

    if (response.status === 200) {
      refreshWatcher.value = true;
    }

    console.log("response", response);
    console.log("Mise à jour réussie :", inputs);
  } catch (error) {
    console.error("Erreur lors de la mise à jour du membre :", error);
  }
};

let initialMemberType = "";
const patchBoat = async () => {
  try {
    let request = {};
    if (inputs.type !== initialMemberType) {
      request.type = inputs.type;
    }

    if (inputs.type !== "supporter") {
      request.boat = inputs.boat;

      if (inputs.hasCoowner === true) {
        request.coowner = inputs.coowner;
      } else {
        if (
          Object.keys(inputs.coowner).length !== 0 &&
          inputs.coowner.constructor === Object
        ) {
          request["delete-coowner"] = true;
        }
      }
    }

    const response = await axios.patch(
      `/api/member/${route.params.id}`,
      request,
    );

    if (response.status === 200) {
      refreshWatcher.value = true;
    }

    console.log("Mise à jour réussie :", inputs);
  } catch (error) {
    console.error("Erreur lors de la mise à jour du bateau :", error);
  }
};

watch(
  () => refreshWatcher.value,
  () => fetchData(),
);

onMounted(async () => {
  await fetchData();
  initialMemberType = inputs.type;
});
</script>

<template>
  <Layout :loading>
    <div class="space-y-10 divide-y divide-gray-900/10">
      <FormCard :submit="patchMember">
        <template #title>Informations de membre</template>
        <template #sub>
          Lorem ipsum dolor sit amet consectetur adipisicing.
        </template>
        <InputComp label="Prénom" v-model="inputs.member.first" />
        <InputComp label="Nom" v-model="inputs.member.last" />
        <InputComp
          label="Courriel"
          x-type="email"
          v-model="inputs.member.email"
        />
        <InputComp
          label="Date de naissance"
          x-type="date"
          v-model="inputs.member.birthdate"
        />
        <InputComp label="Adresse" col="full" v-model="inputs.member.address" />
        <InputComp label="Ville" v-model="inputs.member.city" />
        <InputComp label="Code postal" v-model="inputs.member.postal_code" />
        <InputComp label="Tél." v-model="inputs.member.phone" />
        <InputComp label="Travail" v-model="inputs.member.job" />
      </FormCard>

      <FormCard class="py-10" :submit="patchBoat">
        <template #title>Informations du bateau</template>
        <template #sub>
          Seuls les adhérents actifs ou du Comité Directeur possèdent un bateau.
        </template>
        <MemberTypeRadio
          :member-types="radioPossibilities.memberTypes"
          v-model="inputs.type"
        />
        <template v-if="inputs.type !== 'supporter'">
          <InputComp label="Nom" v-model="inputs.boat.name" />
          <InputComp label="Marque" v-model="inputs.boat.brand" />
          <InputComp label="Modèle" v-model="inputs.boat.model" />
          <InputComp label="Année" x-type="date" v-model="inputs.boat.year" />
          <InputComp
            label="Longueur (m)"
            x-type="number"
            v-model="inputs.boat.length"
          />
          <InputComp
            label="Largeur (m)"
            x-type="number"
            v-model="inputs.boat.width"
          />
          <BoatTypeRadio
            :boat-types="radioPossibilities.boatTypes"
            v-model="inputs.boat.type"
          />
          <HomeportRadio
            :homeports="radioPossibilities.homeports"
            v-model="inputs.boat.homeport"
          />
          <hr class="col-span-full" />
          <Coowner v-model="inputs.hasCoowner">
            <InputComp label="Prénom" v-model="inputs.coowner.first" />
            <InputComp label="Nom" v-model="inputs.coowner.last" />
            <InputComp
              label="Nationalité"
              v-model="inputs.coowner.nationality"
            />
          </Coowner>
        </template>
      </FormCard>
    </div>
  </Layout>
</template>
