(function($) {
  const userLocation = window.userLocation;

  if (userLocation === undefined) {
    locateUser();
  } else {
    console.log('Data from session: ' + userLocation);
    outputUserLocationInfo(userLocation);
  }

  /**
   * Locate user. If not by coordinates, then by ip address. User's city is needed.
   */
  function locateUser() {
    if ('geolocation' in navigator) {
      // Get the geographical position of a user.
      // Since this can compromise privacy, the position is not available unless the user approves it.
      navigator.geolocation.getCurrentPosition(
        (position) => {
          let latitude  = position.coords.latitude,
              longitude = position.coords.longitude;

          console.log('Latitude: ', latitude);
          console.log('Longitude: ', longitude);

          if ($.isNumeric(latitude) && $.isNumeric(longitude)) {
            sendUserGeolocationData(latitude, longitude);
          }
        },
        (PositionError) => {
          console.warn(`Position Error. Code: ${PositionError.code}. Message: ${PositionError.message}`);
          sendUserGeolocationData();
        });

    } else {
      console.log('Geolocation is not enabled on this browser');
      sendUserGeolocationData();
    }
  }

  /**
   * @param geolocation
   * @param latitude
   * @param longitude
   */
  function sendUserGeolocationData(latitude = false, longitude = false) {
    let data = {};

    if (latitude && longitude) {
      data.latitude   = latitude;
      data.longitude  = longitude;
    }

    $.ajax({
      url: '/code/geo-position.php',
      type: 'POST',
      dataType: 'json',
      data: data
    })
    .then(
      function success(data, textStatus, jqXHR) {
        outputUserLocationInfo(data.location);
        console.log(data);
        console.log('AJAX status: ' + textStatus);
      },

      function fail(data, textStatus) {
        console.log('AJAX request failed. Returned status of: ', textStatus);
      }
    );
  }

  /**
   * @param location
   */
  function outputUserLocationInfo(location) {
    if (location) {
      const $geoLocationInfoElement = $('#geoLocationInfo');

      $geoLocationInfoElement.find('span').text(location);
      $geoLocationInfoElement.slideDown(250);
    } else {
      console.log('No data');
    }
  }

})($);
