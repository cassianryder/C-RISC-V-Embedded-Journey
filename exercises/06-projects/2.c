// 06-projects/2.c
// What this file is about:
// 1. A system-dynamics skeleton for the aquaculture project
// 2. Map stocks, flows, relations, feedback, observation, and memory to C
// 3. Only data structures and the main control skeleton are kept here

#define POND_ID_SIZE 16
#define TIMESTAMP_SIZE 20

// Stock: values that describe the current state of the pond.
typedef struct
{
    char pond_id[POND_ID_SIZE];
    char sampled_at[TIMESTAMP_SIZE];
    float temperature;
    float oxygen;
    float ammonia;
    float water_level;
} PondStock;

// Flow: processes that change stocks over time.
typedef struct
{
    float oxygen_input;
    float oxygen_consumption;
    float ammonia_input;
    float ammonia_removal;
    float water_input;
    float water_output;
} PondFlow;

// Parameters: thresholds and constants used by relation and feedback logic.
typedef struct
{
    float oxygen_low_limit;
    float temperature_low_limit;
    float temperature_high_limit;
    float ammonia_high_limit;
    float water_low_limit;
    int sample_interval_seconds;
} PondParams;

// Feedback: control decisions derived from stock and relation functions.
typedef struct
{
    int needs_aeration;
    int needs_water_exchange;
    int feeding_allowed;
    int needs_alert_upload;
} PondFeedback;

// Whole system boundary for one pond.
typedef struct
{
    PondStock stock;
    PondFlow flow;
    PondParams params;
    PondFeedback feedback;
} PondSystem;

int main(void)
{
    PondSystem pond = {0};

    // 1. Define system boundary and initialize stock/parameter values.
    // init_pond_system(&pond);

    while (1)
    {
        // 2. Sense or read current stock values.
        // read_pond_stock(&pond.stock);

        // 3. Compute flows and relations between stock values.
        // update_pond_flow(&pond.flow, pond.stock, pond.params);
        // judge_pond_feedback(&pond.feedback, pond.stock, pond.params);

        // 4. Observe and remember the system state.
        // print_pond_system(pond);
        // save_pond_system_csv(pond);

        // 5. Apply feedback actions.
        // apply_aeration_if_needed(pond.feedback);
        // upload_alert_if_needed(pond.feedback);

        break;
    }

    (void)pond;
    return 0;
}
