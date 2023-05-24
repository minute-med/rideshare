const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"sanctum.csrf-cookie":{"uri":"sanctum\/csrf-cookie","methods":["GET","HEAD"]},"ignition.healthCheck":{"uri":"_ignition\/health-check","methods":["GET","HEAD"]},"ignition.executeSolution":{"uri":"_ignition\/execute-solution","methods":["POST"]},"ignition.updateConfig":{"uri":"_ignition\/update-config","methods":["POST"]},"welcome":{"uri":"\/","methods":["GET","HEAD"]},"contact":{"uri":"contact","methods":["GET","HEAD"]},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"search.index":{"uri":"search","methods":["GET","HEAD"]},"search.submit":{"uri":"search","methods":["POST"]},"profile.edit":{"uri":"profile","methods":["GET","HEAD"]},"profile.update":{"uri":"profile","methods":["PATCH"]},"profile.destroy":{"uri":"profile","methods":["DELETE"]},"profile.trips":{"uri":"profile\/trips","methods":["GET","HEAD"]},"trip.create":{"uri":"trip","methods":["GET","HEAD"]},"trip.store":{"uri":"trip","methods":["POST"]},"trip.show":{"uri":"trip\/{trip}","methods":["GET","HEAD"],"bindings":{"trip":"id"}},"trip.book":{"uri":"trip\/book\/{trip}","methods":["POST"],"bindings":{"trip":"id"}},"trip.approve_booking":{"uri":"trip\/approve\/{trip}","methods":["POST"],"bindings":{"trip":"id"}},"register":{"uri":"register","methods":["GET","HEAD"]},"login":{"uri":"login","methods":["GET","HEAD"]},"password.request":{"uri":"forgot-password","methods":["GET","HEAD"]},"password.email":{"uri":"forgot-password","methods":["POST"]},"password.reset":{"uri":"reset-password\/{token}","methods":["GET","HEAD"]},"password.store":{"uri":"reset-password","methods":["POST"]},"verification.notice":{"uri":"verify-email","methods":["GET","HEAD"]},"verification.verify":{"uri":"verify-email\/{id}\/{hash}","methods":["GET","HEAD"]},"verification.send":{"uri":"email\/verification-notification","methods":["POST"]},"password.confirm":{"uri":"confirm-password","methods":["GET","HEAD"]},"password.update":{"uri":"password","methods":["PUT"]},"logout":{"uri":"logout","methods":["POST"]}}};

if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
