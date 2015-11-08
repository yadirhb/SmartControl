package com.droydsoft.smartcontrol;

import android.content.BroadcastReceiver;
import android.content.Intent;
import android.content.Context;

public class NotificarReciveBoot extends BroadcastReceiver {
	/**
	 * @see android.content.BroadcastReceiver#onReceive(Context,Intent)
	 */
	@Override
	public void onReceive(Context context, Intent intent) {
		Intent servicio = new Intent();
		servicio.setAction("notificar.vibrar.Servicio");
		context.startService(servicio);
	}
}
