<?php
function get_student_name($student) {
  return $student["name"];
}

function get_value_from_attendence($attendence)
{
    return floatval($attendence);
}
$attendences = array_map("get_value_from_attendence", $attendences);
$attendences = json_encode($attendences);

$names = array_map("get_student_name", $students);
$names = json_encode($names);
echo '
const labelsAttendences = '.$names.';

  const dataAttendences = {
    labels: labelsAttendences,
    datasets: [{
      label: "Frequência",
      backgroundColor: "#7c85ff",
      borderColor: "#f5f6ff",
      data: '.$attendences.',
      dataLabels: {
        color: "#000",
        anchor: "end",
        align: "top",
        fontSize: 22,
        fontColor: "#000",
      data: '.$attendences.',

      },
    }, ],
  };

  const configAttendences = {
    type: "bar",
    data: dataAttendences,
    options: {
      plugins: {
        labels: {
          render: function (args) {  
            let max = 100; //Custom maximum value
            return (args.value * 100 / max) + "%";
          },
          font: {
            size: 48
          }
        },
        tooltip: {
          enabled: true,
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          grid: {
            display: false
          }
        },
        x: {
          grid: {
            display: false
          }
        },
      }
    },
  };

  const chartAttendences = new Chart(
    document.getElementById("chartAttendences"),
    configAttendences
  );
';
