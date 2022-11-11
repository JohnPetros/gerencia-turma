<?php
$counts = array_count_values($final_grades);
echo '
const labelsMentions = ["MB", "B", "R", "I"];

const dataMentions = {
  labels: labelsMentions,
  datasets: [{
    label: "2 Módulo DS - Programação Web",
    backgroundColor: ["#0f8f0f", "#2bcc2b", "#cece15", "#d82929"],
    borderColor: "#f5f6ff",
    data: [' . ($counts["MB"] ?? 0) . ', ' . ($counts["B"] ?? 0) . ', ' . ($counts["R"] ?? 0) . ', ' . ($counts["I"] ?? 0) . '],
  }, ],
};

const configMentions = {
  type: "pie",
  data: dataMentions,
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

const chartMentions = new Chart(
  document.getElementById("chartMentions"),
  configMentions
);
';
