<?php
$counts = array_count_values($situations);
echo '
const labelsSituations = ["APROVADO", "RECUPERAÇÃO", "REPROVADO"];

const dataSituations = {
  labels: labelsSituations,
  datasets: [{
    label: "2 Módulo DS - Programação Web",
    backgroundColor: ["#0f8f0f", "#cece15", "#d82929"],
    borderColor: "#f5f6ff",
    data:  [' . ($counts["APROVADO"] ?? 0) . ', ' . ($counts["RECUPERAÇÃO"] ?? 0) . ', ' . ($counts["REPROVADO"] ?? 0) . '],
  }, ],
};

const configSituations = {
  type: "pie",
  data: dataSituations,
  options: {
    plugins: {
      labels: {
        render: "percentage",
        fontSize: 24,
        fontColor: "#f5f6ff",
        precision: 0,
      },
      tooltip: {
        enabled: false,
      },
    },
  },
};

const chartSituations = new Chart(
  document.getElementById("chartSituations"),
  configSituations
);
';
