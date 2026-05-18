
## Page Builder & Content Management
- [ ] Artisan command to clear non-existent sections in prod DB
- [ ] Rules for how to make global changes, aka loading fonts and other stuff for section builders. I think this is something that should be designed by a "Design Architect" agent and not a lowly section builder, but figure it out in general. Likely use the frontend design skill by anthropic and some other stuff

## Refactoring into composer modules
- [ ] 

## Testing
- [ ] Each future composer module should be tested:
  - [ ] **Unit:** Do the helpers and other unit-testable pieces work as expected
  - [ ] **Integration:** Whether the Artisan commands generate the right output as well as testing filament, livewire and the other stuff, all possible with filament testing facades and helper methods.
  - [ ] **End-to-end:** Avoid when possible, most stuff should be doable with filament, livewire test methods.
