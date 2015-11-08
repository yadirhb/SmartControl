package com.droydsoft.smartcontrol.modelo;

import java.util.Vector;

import com.droydsoft.smartcontrol.BtnListView;
import com.droydsoft.smartcontrol.R;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.sax.StartElementListener;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

public class AdaptadorEvento extends ArrayAdapter {

	Activity context;
	Vector<ContentValues> frases;
	private ImageView img_audio;
	private ImageView img_foto;
	private TextView txt_evento;
	private int pos;
	private BtnListView click1;
	private BtnListView click2;

	public AdaptadorEvento(Activity context, Vector<ContentValues> frases,BtnListView click1,BtnListView click2) {
		super(context, R.layout.item_evento, frases);
		this.frases = frases;
		this.context = context;
		this.click1 = click1;
		this.click2 = click2;
	}

	public View getView(int position, View convertView, ViewGroup parent) {
		View item = convertView;
		if (item == null) {
			LayoutInflater inflater = context.getLayoutInflater();
			item = inflater.inflate(R.layout.item_evento, null);
		}
		TextView txt_nombre_user = (TextView) item
				.findViewById(R.id.txt_nombre_user);
		txt_evento = (TextView) item.findViewById(R.id.txt_evento);
		TextView txt_fecha_evento = (TextView) item
				.findViewById(R.id.txt_fecha_evento);
		TextView txt_hora_evento = (TextView) item
				.findViewById(R.id.txt_hora_evento);
		ImageView img_icon_evento = (ImageView) item
				.findViewById(R.id.img_icon_evento);
		img_audio = (ImageView) item.findViewById(R.id.img_audio);
		img_foto = (ImageView) item.findViewById(R.id.img_foto);

		txt_nombre_user
				.setText(frases.get(position).getAsString("nombre_user"));
		txt_fecha_evento.setText(frases.get(position).getAsString(
				"fecha_evento"));
		txt_hora_evento
				.setText(frases.get(position).getAsString("hora_evento"));

		img_icon_evento.setBackgroundResource(tipo_evento(frases.get(position)
				.getAsInteger("tipo_evento"),frases.get(position)
				.getAsString("puerta")));

		//pos = position;
		img_foto.setTag(position);
		img_audio.setTag(position);
		
		img_foto.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				if (click1 != null)
					click1.onBtnClick((Integer) v.getTag());
			}
		});
		
		img_audio.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				if (click2 != null)
					click2.onBtnClick((Integer) v.getTag());
			}
		});		

		return (item);
	}

	public int tipo_evento(int tipo, String puerta) {
		switch (tipo) {
		case 0:
			txt_evento.setText("Emergencia "+puerta);
			activar_foto("SI");
			activar_audio("NO");
			return R.drawable.alerta;
		case 1:
			txt_evento.setText("Entrada "+puerta);
			activar_foto("SI");
			activar_audio("NO");
			return R.drawable.flecha_verde;

		case 2:
			txt_evento.setText("Salida "+puerta);
			activar_audio("NO");
			activar_foto("NO");
			return R.drawable.flecha_roja;

		case 3:
			txt_evento.setText("Entrada Visita "+puerta);
			activar_audio("SI");
			activar_foto("SI");
			return R.drawable.flecha_azul_der;

		case 4:
			txt_evento.setText("Salida Visita "+puerta);
			activar_foto("SI");
			activar_audio("NO");
			return R.drawable.flecha_azul_izq;

		case 5:
			txt_evento.setText("Tarjeta Invalida "+puerta);
			activar_audio("SI");
			activar_foto("SI");
			return R.drawable.cruz_roja;

		case 6:
			txt_evento.setText("Puerta Abierta "+puerta);
			activar_audio("SI");
			activar_foto("SI");
			return R.drawable.alerta;

		case 7:
			txt_evento.setText("Emergencia "+puerta);
			activar_audio("SI");
			activar_foto("SI");
			return R.drawable.alerta;

		case 8:
			txt_evento.setText("Entrada con Pulsador "+puerta);
			activar_foto("SI");
			activar_audio("SI");
			return R.drawable.flecha_gris_der;

		case 9:
			txt_evento.setText("Salida con Pulsador "+puerta);
			activar_foto("SI");
			activar_audio("NO");
			return R.drawable.flecha_gris_izq;
		}

		return R.drawable.alerta;
	}

	public void activar_audio(String url) {
		if (url.equals("NO")) {
			img_audio.setVisibility(View.INVISIBLE);
		} else {
			img_audio.setVisibility(View.VISIBLE);
		}
	}

	public void activar_foto(String url) {
		if (url.equals("NO")) {
			img_foto.setVisibility(View.INVISIBLE);
		} else {
			img_foto.setVisibility(View.VISIBLE);
		}
	}

}
