<template>
  <div class="container">
    <form @submit.prevent="playHand">
      <div class="form-group">
        <label for="name">Name:</label>
        <input v-validate="'required'" name="name" type="text" v-model="name" />
        <span>{{ errors.first('name') }}</span>
      </div>
      <div>
        <label for="card">Hand of Cards:</label>
        <input v-validate="'required|min:1'" name="hand" type="text" v-model="userHand" />
        <span>{{ errors.first('hand') }}</span>
      </div>
      <div>
        <button>Play</button>
      </div>
      <div>
        <p>
          Player's hand:
          <span v-for="(number, index) in userHand" :key="index">{{ number }}&nbsp;</span>
        </p>
        <p>
          Generated hand:
          <span
            v-for="(number, index) in generatedNumbers"
            :key="index"
          >{{ number }}&nbsp;</span>
        </p>
      </div>
      <div>
        <p>Player score: {{ playerScore }}</p>
        <p>Generated score: {{ generatedScore }}</p>
      </div>
    </form>
  </div>
</template>

<script>
import VeeValidate from "vee-validate";

Vue.prototype.$setErrorsFromResponse = function(errorResponse) {
    // Only allow this function to be run if the validator exists
    // and an error response was given.
    if(!this.hasOwnProperty('$validator') || !errorResponse) {
        return;
    }

    // Check for error fields.
    let keys = Object.keys(errorResponse);

    if(keys.length === 0) {
        return;
    }

    // Clear errors in the Vue object.
    this.$validator.errors.clear();

    let errorFields = [];
    keys.forEach(field => {
        // If the field has a dot, it means it's part of an array input
        if(field.indexOf(".") > -1) {
            // Reconstruct the original field name. ex. 'urls[]'
            let fieldOriginalName = field.split(".")[0] + "[]";
            // If we don't have it in the resultant error fields, add it, otherwise
            // only push the error as an array to the original response object.
            if(!errorFields[fieldOriginalName]) {
                errorFields.push(fieldOriginalName);
                errorResponse[fieldOriginalName] = [errorResponse[field]]
            } else {
                errorResponse[fieldOriginalName].push(errorResponse[field]);
            }
        } else {
            // Otherwise just add it to the list.
            errorFields.push(field);
        }
    });

    errorFields.forEach(field => {
        let errorValue = errorResponse[field];
        let errorString = errorValue instanceof Array ? errorValue.join(', ') : errorValue;
        this.$validator.errors.add({field: field, msg: errorString});
    });
}

Vue.use(VeeValidate);

const validCards = [
  "2",
  "3",
  "4",
  "5",
  "6",
  "7",
  "8",
  "9",
  "10",
  "J",
  "Q",
  "K",
  "A"
];

export default {
  data() {
    return {
      name: "",
      userHand: "",
      generatedNumbers: [],
      playerScore: 0,
      generatedScore: 0
    };
  },
  mounted() {
    console.log("Component mounted.");
  },
  computed: {
    userHandArr: function() {
      return this.userHand.split(" ");
    }
  },
  methods: {
    playHand() {
      this.$validator.validate().then(valid => {
        if (!valid) {
          alert("Please enter the valid data");
        } else {
          this.generateCards(this.userHandArr.length);
        }
      });
    },
    generateCards(totalHand) {
      this.generatedNumbers = [];
      let i = 0;

      while (i < totalHand) {
        const randomNumber =
          Math.floor(Math.random() * validCards.length);
        if (this.generatedNumbers.indexOf(randomNumber) === -1) {
          this.generatedNumbers.push(validCards[randomNumber]);
          i++;
        }
      }

      this.generateScore();
    },
    generateScore() {

        const userScoreIndex = [];
        const generatedScoreIndex = [];

      this.playerScore = 0;
      this.generatedScore = 0;

      this.userHandArr.forEach(score => {
        userScoreIndex.push(validCards.indexOf(score));
      });

      this.generatedNumbers.forEach(score => {
          generatedScoreIndex.push(validCards.indexOf(score));
      });

      userScoreIndex.forEach((score, index) => {
          if (score > generatedScoreIndex[index]) {
              this.playerScore += 1;
          }
          else if (score < generatedScoreIndex[index]) {
              this.generatedScore += 1;
          }
      });

      let self = this;
      // save the result in the DB
      axios.post('/api/score', {
            name: self.name,
            user_score: self.playerScore,
            generated_score: self.generatedScore
        }).then(function(response) {
            console.log(response);
        }).catch(function(error) {
            self.$setErrorsFromResponse(error.response.data.errors);
        });
    }
  }
};
</script>
