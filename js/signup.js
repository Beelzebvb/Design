const nextSteps = document.querySelectorAll(".next-step");
const previousSteps = document.querySelectorAll(".previous-step");
const signupForms = document.querySelectorAll(".signup-form");

const nextSport = document.getElementById("next-sport");
const previousSport = document.getElementById("previous-sport");

const steps = document.querySelector(".steps");
const textInputs = document.querySelectorAll(
  'input[type="text"], input[type="password"]'
);

const sports = ["basketball", "soccer"];
let currentSpotIndex = 0;
let level = 0;

updateSportChoice();
goToStep(level);

nextSteps.forEach((nextStep) => {
  nextStep.addEventListener("click", (e) => {
    e.preventDefault();
    level++;
    updateStep(level);
    signupForms.forEach((signupForm) => {
      signupForm.style.transition = "all 0.6s ease";
      signupForm.style.transform = `translateX(${-level * 100}%)`;
    });
  });
});

previousSteps.forEach((previousStep) => {
  previousStep.addEventListener("click", (e) => {
    e.preventDefault();
    level--;
    updateStep(level);

    signupForms.forEach((signupForm) => {
      signupForm.style.transition = "all 0.6s ease";
      signupForm.style.transform = `translateX(${-level * 100}%)`;
    });
  });
});

for (let i = 0; i < steps.children.length; i++) {
  const step = steps.children[i];

  step.addEventListener("click", () => {
    goToStep(i);
  });
}

previousSport.addEventListener("click", () => {
  currentSpotIndex =
    currentSpotIndex - 1 >= 0 ? currentSpotIndex - 1 : sports.length - 1;
  updateSportChoice();
});

nextSport.addEventListener("click", () => {
  currentSpotIndex = (currentSpotIndex + 1) % sports.length;
  updateSportChoice();
});

textInputs.forEach((input) => {
  input.addEventListener("keyup", (e) => {
    if (input.value.length === 0) {
      input.style.border = "1px solid red";
      input.style.backgroundColor = "#ca535336";
    } else {
      input.style.border = "1px solid #267df0";
      input.style.backgroundColor = "#5386ca36";
    }
  });
});

function updateStep(index) {
  Array.prototype.slice.call(steps.children).forEach((step) => {
    step.children[0].classList.remove("number-active");
  });
  steps.children[index].children[0].classList.add("number-active");
}

function goToStep(index) {
  signupForms.forEach((signupForm) => {
    //TODO Remove after fix
    signupForm.style.transition = "all 0.6s ease";
    signupForm.style.transform = `translateX(${-index * 100}%)`;
  });
  level = index;
  updateStep(index);
}

function updateSportChoice() {
  const sportChoice = document.getElementById("sport-choice");

  let sportBackgroundURL = null;

  switch (sports[currentSpotIndex]) {
    case "soccer":
      sportBackgroundURL =
        "https://cdn.dribbble.com/users/31664/screenshots/6004976/var_dribbble.gif";
      break;
    case "basketball":
      sportBackgroundURL =
        "https://i.pinimg.com/originals/66/cf/0f/66cf0fa9379d11c0d49f32ae63247d94.gif";
      break;
  }

  sportChoice.style.backgroundImage = `url(${sportBackgroundURL})`;
}
