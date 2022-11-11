<?php
$lessons = 198;

function show_class_average_grade($class_average_grade)
{
  $class_average_mention_grade = convert_number_grade_to_mention_grade($class_average_grade);
  echo '
  <div class="class-average-grade">
    <strong>' . $class_average_mention_grade . '</strong>
    <h2>Média Geral da Turma</h2>
  </div>
  ';
}

function convert_number_grade_to_mention_grade($grade)
{
  switch (true) {
    case ($grade <= 2.5):
      return "I";
      break;
    case ($grade <= 5):
      return "R";
      break;
    case ($grade <= 7.5):
      return "B";
      break;
    default:
      return "MB";
  }
}

function convert_mention_grade_to_number_grades($grade)
{
  switch ($grade) {
    case "MB":
      return 10;
    case "B":
      return 7.5;
    case "R":
      return 5;
    case "I":
      return 2.5;
    default:
      return;
  }
}


function calculate_class_average_grade($mention_grades)
{
  $number_grades = array_map("convert_mention_grade_to_number_grades", $mention_grades);
  $sum_grades = array_reduce($number_grades, function ($accumulator, $grade) {
    $accumulator += $grade;
    return $accumulator;
  }, 0);

  return number_format(($sum_grades / count($number_grades)), 1);
}

if (isset($_POST["submit"])) {
  $final_grades = $_POST["final-grade"];
  $situations = $_POST["situation"];
  $attendences = $_POST["attendence"];

  $class_average_grade = calculate_class_average_grade($final_grades);

  $lessons = $_POST["lessons"];

}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="src/css/styles.css?=<?php echo time() ?>" />
  <script defer src="src/js/main.js?=<?php echo time() ?>"></script>
  <title>Boletim da turma</title>
</head>

<body>
  <header>
    <img class="logo" src="https://nsa.cps.sp.gov.br/logos/195.Bmp" alt="" />
    <h1>Boletim da turma do 2º módulo em Programação Web</h1>
  </header>

  <form action="" method="post">
    <div class="caption">
      <fieldset>
        <legend>Legenda:</legend>
        <div>MB = Muito Bom</div>
        <div>B = Bom</div>
        <div>R = regular</div>
        <div>I = Insatisfatório</div>
        <div>Frequência < 25% = Reprovação automática</div>
      </fieldset>
      <div>
        Nº de aulas no ano:
        <input class="lessons" type="number" name="lessons" id="lessons" value="<?php echo $lessons ?>" min="1" max="200" />
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th>Aluno</th>
          <th>1º Bimestre</th>
          <th>2º Bimestre</th>
          <th>3º Bimestre</th>
          <th>4º Bimestre</th>
          <th>Média Final</th>
          <th>Faltas</th>
          <th>Frequência</th>
          <th>Situação</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include_once("./src/includes/data.php");
        $grades = array("MB", "B", "R", "I");
        $student_grades = array();
        if (isset($_COOKIE["grades"])) {
          $student_grades = json_decode($_COOKIE["grades"]);
        } else {
          foreach ($students as $student) {
            array_push($student_grades,  $student["grades"]);
          }
        }

        if (isset($_COOKIE["absences"])) {
          $student_absences = json_decode($_COOKIE["absences"]);
        }

        foreach ($students as $index => $student) {
          $absences = $student_absences[$index] ?? $student["absences"];
          $grade_tables = "";
          foreach ($student_grades[$index] as $student_grade) {
            $options = "";
            foreach ($grades as $grade) {

              $selected = $grade == $student_grade ? "selected" : "";

              $options .= '<option ' . $selected . ' value="' . $grade . '" >' . $grade . '</option>';
            }
            $grade_tables .= '
              <td class="grade-table">
                <select class="grade">
                 ' . $options . '
                </select>
              </td>
              ';
          }

          echo '<tr class="row">
            <td class="student">
              <img
                src="src/images/' . $student["image"] . '"
                alt="Imagem do aluno"
              /><span class="name">' . $student["name"] . '</span>
            </td>
            ' . $grade_tables . '
            <td class="grade-table">
              <input
                class="grade final-grade"
                type="text"
                name="final-grade[]"
                value="-"
              />
            </td>
            <td class="attendance-table">
              <input
                class="absences"
                type="number"
                name="attendance-percentage"
                value="' . $absences . '"
                max="' . $lessons . '"
              />
            </td>
            <td class="attendance-table">
              <input
                class="attendance attendance-percentage"
                text="text"
                name="attendence[]"
                value="12%"
              />
            </td>
            <td class="situation-table">
            <input
              class="situation"
              text="text"
              name="situation[]"
              value="REPROVADO"
            />
            </td>
          </tr>';
        }
        ?>
      </tbody>

    </table>

    <button name="submit" type="submit">Atualizar gráficos</button>
  </form>

  <?php if (isset($class_average_grade)) show_class_average_grade($class_average_grade) ?>

  <div class="canvas-container">
    <canvas id="chartSituations"></canvas>
  </div>

  <div class="canvas-container">
    <canvas id="chartMentions"></canvas>
  </div>

  <div class="canvas-container" id="attendences">
    <canvas id="chartAttendences"></canvas>
  </div>

  <div class="canvas-container">
    <canvas id="chartLineMentions"></canvas>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://unpkg.com/chart.js-plugin-labels-dv/dist/chartjs-plugin-labels.min.js"></script>

  <script>
    <?php
    include_once("./src/includes/chartMentions.php");
    include_once("./src/includes/chartSituations.php");
    include_once("./src/includes/chartAttendences.php");
    ?>
  </script>

</body>

</html>