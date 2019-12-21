<?php

namespace client\entity;

class EntityHelpers
{
	const NETWORK_ID = -1;

	const DATA_TYPE_BYTE = 0;
	const DATA_TYPE_SHORT = 1;
	const DATA_TYPE_INT = 2;
	const DATA_TYPE_FLOAT = 3;
	const DATA_TYPE_STRING = 4;
	const DATA_TYPE_SLOT = 5;
	const DATA_TYPE_POS = 6;
	const DATA_TYPE_LONG = 7;
	const DATA_TYPE_VECTOR3F = 8;

	const DATA_FLAGS = 0;
	const DATA_HEALTH = 1; //int (minecart/boat)
	const DATA_VARIANT = 2; //int
	const DATA_COLOR = 3, DATA_COLOUR = 3; //byte
	const DATA_NAMETAG = 4; //string
	const DATA_OWNER_EID = 5; //long
	const DATA_TARGET_EID = 6; //long
	const DATA_AIR = 7; //short
	const DATA_POTION_COLOR = 8; //int (ARGB!)
	const DATA_POTION_AMBIENT = 9; //byte
	/* 10 (byte) */
	const DATA_HURT_TIME = 11; //int (minecart/boat)
	const DATA_HURT_DIRECTION = 12; //int (minecart/boat)
	const DATA_PADDLE_TIME_LEFT = 13; //float
	const DATA_PADDLE_TIME_RIGHT = 14; //float
	const DATA_EXPERIENCE_VALUE = 15; //int (xp orb)
	const DATA_MINECART_DISPLAY_BLOCK = 16; //int (id | (data << 16))
	const DATA_MINECART_DISPLAY_OFFSET = 17; //int
	const DATA_MINECART_HAS_DISPLAY = 18; //byte (must be 1 for minecart to show block inside)

	//TODO: add more properties

	const DATA_ENDERMAN_HELD_ITEM_ID = 23; //short
	const DATA_ENDERMAN_HELD_ITEM_DAMAGE = 24; //short
	const DATA_ENTITY_AGE = 25; //short

	/* 27 (byte) player-specific flags
	 * 28 (int) player "index"?
	 * 29 (block coords) bed position */
	const DATA_FIREBALL_POWER_X = 30; //float
	const DATA_FIREBALL_POWER_Y = 31;
	const DATA_FIREBALL_POWER_Z = 32;
	/* 33 (unknown)
	 * 34 (float) fishing bobber
	 * 35 (float) fishing bobber
	 * 36 (float) fishing bobber */
	const DATA_POTION_AUX_VALUE = 37; //short
	const DATA_LEAD_HOLDER_EID = 38; //long
	const DATA_SCALE = 39; //float
	const DATA_INTERACTIVE_TAG = 40; //string (button text)
	const DATA_NPC_SKIN_ID = 41; //string
	const DATA_URL_TAG = 42; //string
	const DATA_MAX_AIR = 43; //short
	const DATA_MARK_VARIANT = 44; //int
	/* 45 (byte) container stuff
	 * 46 (int) container stuff
	 * 47 (int) container stuff */
	const DATA_BLOCK_TARGET = 48; //block coords (ender crystal)
	const DATA_WITHER_INVULNERABLE_TICKS = 49; //int
	const DATA_WITHER_TARGET_1 = 50; //long
	const DATA_WITHER_TARGET_2 = 51; //long
	const DATA_WITHER_TARGET_3 = 52; //long
	/* 53 (short) */
	const DATA_BOUNDING_BOX_WIDTH = 54; //float
	const DATA_BOUNDING_BOX_HEIGHT = 55; //float
	const DATA_FUSE_LENGTH = 56; //int
	const DATA_RIDER_SEAT_POSITION = 57; //vector3f
	const DATA_RIDER_ROTATION_LOCKED = 58; //byte
	const DATA_RIDER_MAX_ROTATION = 59; //float
	const DATA_RIDER_MIN_ROTATION = 60; //float
	const DATA_AREA_EFFECT_CLOUD_RADIUS = 61; //float
	const DATA_AREA_EFFECT_CLOUD_WAITING = 62; //int
	const DATA_AREA_EFFECT_CLOUD_PARTICLE_ID = 63; //int
	/* 64 (int) shulker-related */
	const DATA_SHULKER_ATTACH_FACE = 65; //byte
	/* 66 (short) shulker-related */
	const DATA_SHULKER_ATTACH_POS = 67; //block coords
	const DATA_TRADING_PLAYER_EID = 68; //long

	/* 70 (byte) command-block */
	const DATA_COMMAND_BLOCK_COMMAND = 71; //string
	const DATA_COMMAND_BLOCK_LAST_OUTPUT = 72; //string
	const DATA_COMMAND_BLOCK_TRACK_OUTPUT = 73; //byte
	const DATA_CONTROLLING_RIDER_SEAT_NUMBER = 74; //byte
	const DATA_STRENGTH = 75; //int
	const DATA_MAX_STRENGTH = 76; //int
	/* 77 (int)
	 * 78 (int) */


	const DATA_FLAG_ONFIRE = 0;
	const DATA_FLAG_SNEAKING = 1;
	const DATA_FLAG_RIDING = 2;
	const DATA_FLAG_SPRINTING = 3;
	const DATA_FLAG_ACTION = 4;
	const DATA_FLAG_INVISIBLE = 5;
	const DATA_FLAG_TEMPTED = 6;
	const DATA_FLAG_INLOVE = 7;
	const DATA_FLAG_SADDLED = 8;
	const DATA_FLAG_POWERED = 9;
	const DATA_FLAG_IGNITED = 10;
	const DATA_FLAG_BABY = 11;
	const DATA_FLAG_CONVERTING = 12;
	const DATA_FLAG_CRITICAL = 13;
	const DATA_FLAG_CAN_SHOW_NAMETAG = 14;
	const DATA_FLAG_ALWAYS_SHOW_NAMETAG = 15;
	const DATA_FLAG_IMMOBILE = 16, DATA_FLAG_NO_AI = 16;
	const DATA_FLAG_SILENT = 17;
	const DATA_FLAG_WALLCLIMBING = 18;
	const DATA_FLAG_CAN_CLIMB = 19;
	const DATA_FLAG_SWIMMER = 20;
	const DATA_FLAG_CAN_FLY = 21;
	const DATA_FLAG_RESTING = 22;
	const DATA_FLAG_SITTING = 23;
	const DATA_FLAG_ANGRY = 24;
	const DATA_FLAG_INTERESTED = 25;
	const DATA_FLAG_CHARGED = 26;
	const DATA_FLAG_TAMED = 27;
	const DATA_FLAG_LEASHED = 28;
	const DATA_FLAG_SHEARED = 29;
	const DATA_FLAG_GLIDING = 30;
	const DATA_FLAG_ELDER = 31;
	const DATA_FLAG_MOVING = 32;
	const DATA_FLAG_BREATHING = 33;
	const DATA_FLAG_CHESTED = 34;
	const DATA_FLAG_STACKABLE = 35;
	const DATA_FLAG_SHOWBASE = 36;
	const DATA_FLAG_REARING = 37;
	const DATA_FLAG_VIBRATING = 38;
	const DATA_FLAG_IDLING = 39;
	const DATA_FLAG_EVOKER_SPELL = 40;
	const DATA_FLAG_CHARGE_ATTACK = 41;

	const DATA_FLAG_LINGER = 45;
}