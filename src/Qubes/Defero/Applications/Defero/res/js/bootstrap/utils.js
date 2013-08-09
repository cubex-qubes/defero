function bsUtilPreventDefault(event, _this) {
  if(_this.data("prevent-default")) {
    event.preventDefault();
  }
}
