const rows = document.querySelectorAll(".row");
const selects = document.querySelectorAll("select");
const inputsNumbers = document.querySelectorAll("input[type=number]");
const form = document.querySelector("form");
let grades = [];
let absences = [];

function setCookie(name, value) {
  const expire = new Date();
  expire.setTime(expire.getTime() + 60 * 1000);
  document.cookie = `${name}=${JSON.stringify(
    value
  )}; expires=${expire.toUTCString()}; path=/`;
}

function storageGrades(gradeMentions) {
  grades.push(gradeMentions);
  console.log(grades);
  if (grades.length == rows.length) {
    setCookie("grades", grades);
    grades = [];
  }
}

function storageAbsences(absences_) {
  absences.push(absences_);
  if (absences.length == rows.length) {
    setCookie("absences", absences);
    absences = [];
  }
}

function convertGradeMentionToGradeNumber(gradeMention) {
  const tableGrades = {
    MB: 10,
    B: 7.5,
    R: 5,
    I: 2.5,
  };
  return tableGrades[gradeMention];
}

function convertGradeNumberToGradeMention(finalGradeNumber) {
  if (finalGradeNumber <= 2.5) {
    return "I";
  } else if (finalGradeNumber <= 5) {
    return "R";
  } else if (finalGradeNumber <= 7.5) {
    return "B";
  } else {
    return "MB";
  }
}

function calculateFinalGradeMention(gradeMentions) {
  if (gradeMentions.includes("-")) return null;

  const gradeNumbers = gradeMentions.map(convertGradeMentionToGradeNumber);
  const sumGradeNumbers = gradeNumbers.reduce(
    (prev, current) => prev + current,
    0
  );
  const finalGradeNumber = (sumGradeNumbers / 4).toFixed(1);
  const finalGradeMention = convertGradeNumberToGradeMention(finalGradeNumber);
  return finalGradeMention;
}

function calculateSituation(finalGradeMention) {
  switch (finalGradeMention) {
    case "MB":
    case "B":
      return "APROVADO";
    case "R":
      return "RECUPERAÇÃO";
    case "I":
      return "REPROVADO";
    default:
      return;
  }
}

function updateSituationStyle(situationElement, situation) {
  const tableClasses = {
    APROVADO: "passed",
    RECUPERAÇÃO: "recuperation",
    REPROVADO: "rejected",
  };

  situationElement.classList.remove(...Object.values(tableClasses), undefined);
  situationElement.classList.add(tableClasses[situation]);
}

function updateSituation(row, finalGradeMention) {
  const situationElement = row.querySelector(".situation");
  const situation = calculateSituation(finalGradeMention);
  situationElement.value = situation;
  updateSituationStyle(situationElement, situation);
}

function updateFinalGrade(row) {
  const grades = row.querySelectorAll("select.grade");
  let gradeMentions = [];
  grades.forEach((grade) => {
    gradeMentions.push(grade.value);
  });
  const finalGradeElement = row.querySelector(".final-grade");

  const finalGradeMention = calculateFinalGradeMention(gradeMentions);

  finalGradeElement.value = finalGradeMention || "-";

  if (finalGradeMention !== "-") {
    updateSituation(row, finalGradeMention);
  }

  storageGrades(gradeMentions);
}

function verifyPercentage() {}

function updateAttendancePercentage(row) {
  const absences = row.querySelector(".absences").value;
  const lessons = document.querySelector("#lessons").value;
  const updateAttendancePercentageElement = row.querySelector(
    ".attendance-percentage"
  );
  const percentage = (((lessons - absences) / lessons) * 100).toFixed(2);
  updateAttendancePercentageElement.value = percentage + "%";

  const isAttendancePercentageEnough = percentage > 25;
  if (!isAttendancePercentageEnough) {
    updateSituation(row, "I");
  }

  storageAbsences(absences);
}

function updateRows() {
  rows.forEach((row) => {
    updateFinalGrade(row);
    updateAttendancePercentage(row);
  });
}

function handleSubmit(event) {
  const finalGrades = Array.from(
    document.querySelectorAll(".final-grade"),
    ({ value }) => value
  );
  console.log(finalGrades);
  if (finalGrades.includes("-")) {
    alert("As notas finais dos alunos devem estar preenchidos");
    return event.preventDefault();
  }

  form.submit();
}

selects.forEach((select) => select.addEventListener("change", updateRows));
inputsNumbers.forEach((input) => input.addEventListener("change", updateRows));
form.addEventListener("submit", handleSubmit);

updateRows();
